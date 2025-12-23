<?php

namespace App\Services\Withdrawal;

use App\DTOs\CreateWithdrawalDTO;
use App\Models\Retiro;
use App\Models\TransaccionBilletera;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    public function __construct(
        private WalletService $walletService,
        private WithdrawalValidationService $validationService
    ) {}

    /**
     * Crear solicitud de retiro
     */
    public function createWithdrawal(User $user, CreateWithdrawalDTO $dto): Retiro
    {
        // Validar que puede retirar
        $errors = $this->validationService->getValidationErrors($user, $dto->monto);
        if (!empty($errors)) {
            throw new \Exception(implode('. ', $errors));
        }

        return DB::transaction(function () use ($user, $dto) {
            $billetera = $this->walletService->getOrCreateWallet($user);

            // Crear retiro
            $retiro = Retiro::create([
                'codigo_retiro' => $this->generateCode(),
                'usuario_id' => $user->id,
                'monto_solicitado' => $dto->monto,
                'metodo_pago' => $dto->metodoPago,
                'datos_pago' => json_encode($dto->datosPago),
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'notas_aprobacion' => $dto->notas,
            ]);

            // Bloquear fondos
            $this->walletService->blockFunds($billetera, $dto->monto);

            return $retiro;
        });
    }

    /**
     * Aprobar solicitud de retiro
     */
    public function approveWithdrawal(Retiro $retiro, User $admin): void
    {
        if ($retiro->estado !== 'pendiente' && $retiro->estado !== 'en_revision') {
            throw new \Exception('Solo se pueden aprobar retiros pendientes o en revisión');
        }

        $retiro->update([
            'estado' => 'aprobado',
            'fecha_aprobacion' => now(),
            'aprobado_por' => $admin->id,
            'monto_aprobado' => $retiro->monto_solicitado,
            'comision' => 0, // Sin comisión por ahora
            'monto_neto' => $retiro->monto_solicitado,
        ]);
    }

    /**
     * Rechazar solicitud de retiro
     */
    public function rejectWithdrawal(Retiro $retiro, User $admin, string $motivo): void
    {
        if (!in_array($retiro->estado, ['pendiente', 'en_revision', 'aprobado'])) {
            throw new \Exception('No se puede rechazar este retiro');
        }

        DB::transaction(function () use ($retiro, $admin, $motivo) {
            // Desbloquear fondos (tolerante a datos históricos sin saldo bloqueado)
            $billetera = $retiro->usuario->billetera;
            if ($billetera && $billetera->saldo_bloqueado > 0) {
                $montoDesbloquear = min($billetera->saldo_bloqueado, $retiro->monto_solicitado);
                $this->walletService->unblockFunds($billetera, $montoDesbloquear);
            }

            $retiro->update([
                'estado' => 'rechazado',
                'fecha_rechazo' => now(),
                'aprobado_por' => $admin->id,
                'motivo_rechazo' => $motivo,
            ]);
        });
    }

    /**
     * Marcar retiro como pagado
     */
    public function markAsPaid(Retiro $retiro, User $admin, UploadedFile $comprobante, ?string $notas = null): void
    {
        if ($retiro->estado !== 'aprobado') {
            throw new \Exception('Solo se pueden pagar retiros aprobados');
        }

        DB::transaction(function () use ($retiro, $admin, $comprobante, $notas) {
            // Guardar comprobante
            $path = $this->storeProof($retiro, $comprobante);

            // Descontar del saldo bloqueado (tolerante a datos históricos)
            $billetera = $retiro->usuario->billetera;
            if ($billetera) {
                $montoDescontar = min($billetera->saldo_bloqueado, $retiro->monto_solicitado);
                $saldoAnterior = $billetera->saldo_bloqueado;

                if ($montoDescontar > 0) {
                    $billetera->decrement('saldo_bloqueado', $montoDescontar);
                }

                // Registrar transacción de retiro
                TransaccionBilletera::create([
                    'codigo_transaccion' => $this->generateTransactionCode(),
                    'billetera_id' => $billetera->id,
                    'usuario_id' => $retiro->usuario_id,
                    'tipo' => 'retiro',
                    'monto' => $retiro->monto_solicitado,
                    'naturaleza' => 'debito',
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_posterior' => $billetera->fresh()->saldo_bloqueado,
                    'descripcion' => "Retiro {$retiro->codigo_retiro} - {$retiro->metodo_pago}",
                    'referencia_type' => Retiro::class,
                    'referencia_id' => $retiro->id,
                    'procesado_por' => $admin->id,
                    'fecha_transaccion' => now(),
                ]);
            }

            $retiro->update([
                'estado' => 'pagado',
                'fecha_pago' => now(),
                'pagado_por' => $admin->id,
                'comprobante_pago' => $path,
                'notas_aprobacion' => $notas ?? $retiro->notas_aprobacion,
            ]);
        });
    }

    /**
     * Cancelar retiro (por el usuario)
     */
    public function cancelWithdrawal(Retiro $retiro): void
    {
        if ($retiro->estado !== 'pendiente') {
            throw new \Exception('Solo se pueden cancelar retiros pendientes');
        }

        DB::transaction(function () use ($retiro) {
            // Desbloquear fondos (tolerante a datos históricos sin saldo bloqueado)
            $billetera = $retiro->usuario->billetera;
            if ($billetera && $billetera->saldo_bloqueado > 0) {
                $montoDesbloquear = min($billetera->saldo_bloqueado, $retiro->monto_solicitado);
                $this->walletService->unblockFunds($billetera, $montoDesbloquear);
            }

            $retiro->update([
                'estado' => 'cancelado',
            ]);
        });
    }

    /**
     * Generar código único de retiro
     */
    private function generateCode(): string
    {
        $date = now()->format('Ymd');
        $count = Retiro::whereDate('created_at', today())->count() + 1;

        return sprintf('RET-%s-%05d', $date, $count);
    }

    /**
     * Generar código de transacción
     */
    private function generateTransactionCode(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));

        return "TRX-{$date}-{$random}";
    }

    /**
     * Guardar comprobante de pago
     */
    private function storeProof(Retiro $retiro, UploadedFile $file): string
    {
        $directory = public_path("uploads/retiros/{$retiro->id}");

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'comprobante_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return "uploads/retiros/{$retiro->id}/{$filename}";
    }

    /**
     * Obtener URL del comprobante
     */
    public function getProofUrl(Retiro $retiro): ?string
    {
        if (!$retiro->comprobante_pago) {
            return null;
        }

        return asset($retiro->comprobante_pago);
    }

    /**
     * Obtener estadísticas de retiros para admin
     */
    public function getStats(): array
    {
        return [
            'pendientes' => [
                'cantidad' => Retiro::where('estado', 'pendiente')->count(),
                'monto' => Retiro::where('estado', 'pendiente')->sum('monto_solicitado'),
            ],
            'aprobados' => [
                'cantidad' => Retiro::where('estado', 'aprobado')->count(),
                'monto' => Retiro::where('estado', 'aprobado')->sum('monto_solicitado'),
            ],
            'pagados_hoy' => [
                'cantidad' => Retiro::where('estado', 'pagado')->whereDate('fecha_pago', today())->count(),
                'monto' => Retiro::where('estado', 'pagado')->whereDate('fecha_pago', today())->sum('monto_solicitado'),
            ],
            'pagados_mes' => [
                'cantidad' => Retiro::where('estado', 'pagado')->whereMonth('fecha_pago', now()->month)->count(),
                'monto' => Retiro::where('estado', 'pagado')->whereMonth('fecha_pago', now()->month)->sum('monto_solicitado'),
            ],
        ];
    }
}
