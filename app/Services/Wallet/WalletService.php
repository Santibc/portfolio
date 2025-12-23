<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Billetera;
use App\Models\Dividendo;
use App\Models\TransaccionBilletera;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Crear billetera para un usuario
     */
    public function createWallet(User $user): Billetera
    {
        return Billetera::create([
            'usuario_id' => $user->id,
            'saldo_disponible' => 0,
            'saldo_bloqueado' => 0,
            'saldo_invertido' => 0,
            'retornos_acumulados' => 0,
            'dividendos_pendientes' => 0,
        ]);
    }

    /**
     * Obtener o crear billetera para un usuario
     */
    public function getOrCreateWallet(User $user): Billetera
    {
        $billetera = Billetera::where('usuario_id', $user->id)->first();

        if (!$billetera) {
            $billetera = $this->createWallet($user);
        }

        return $billetera;
    }

    /**
     * Obtener resumen de balance formateado
     */
    public function getBalanceSummary(User $user): array
    {
        $billetera = $this->getOrCreateWallet($user);

        // Obtener el próximo dividendo programado (el más cercano)
        $proximoDividendo = Dividendo::where('usuario_id', $user->id)
            ->where('estado', 'programado')
            ->orderBy('fecha_programada', 'asc')
            ->with('proyecto')
            ->first();

        $montoDividendoPendiente = $proximoDividendo ? $proximoDividendo->monto : 0;
        $fechaDividendo = $proximoDividendo && $proximoDividendo->fecha_programada
            ? $proximoDividendo->fecha_programada->format('d/m/Y')
            : null;
        $proyectoDividendo = $proximoDividendo && $proximoDividendo->proyecto
            ? $proximoDividendo->proyecto->nombre
            : null;

        return [
            'saldo_disponible' => [
                'valor' => $billetera->saldo_disponible,
                'formateado' => '$' . number_format($billetera->saldo_disponible, 0, ',', '.'),
                'titulo' => 'Saldo Disponible',
                'icono' => 'fas fa-wallet',
                'color' => 'success',
                'descripcion' => 'Disponible para invertir o retirar',
            ],
            'saldo_invertido' => [
                'valor' => $billetera->saldo_invertido,
                'formateado' => '$' . number_format($billetera->saldo_invertido, 0, ',', '.'),
                'titulo' => 'Capital Invertido',
                'icono' => 'fas fa-hand-holding-usd',
                'color' => 'primary',
                'descripcion' => 'Inversiones activas',
            ],
            'saldo_bloqueado' => [
                'valor' => $billetera->saldo_bloqueado,
                'formateado' => '$' . number_format($billetera->saldo_bloqueado, 0, ',', '.'),
                'titulo' => 'Saldo Bloqueado',
                'icono' => 'fas fa-lock',
                'color' => 'warning',
                'descripcion' => 'Retiros pendientes de aprobación',
            ],
            'retornos_acumulados' => [
                'valor' => $billetera->retornos_acumulados,
                'formateado' => '$' . number_format($billetera->retornos_acumulados, 0, ',', '.'),
                'titulo' => 'Retornos Acumulados',
                'icono' => 'fas fa-chart-line',
                'color' => 'info',
                'descripcion' => 'Ganancias totales históricas',
            ],
            'dividendos_pendientes' => [
                'valor' => $montoDividendoPendiente,
                'formateado' => '$' . number_format($montoDividendoPendiente, 0, ',', '.'),
                'titulo' => 'Próximo Dividendo',
                'icono' => 'fas fa-coins',
                'color' => 'warning',
                'descripcion' => $fechaDividendo
                    ? "Fecha: {$fechaDividendo}" . ($proyectoDividendo ? " - {$proyectoDividendo}" : '')
                    : 'Sin dividendos programados',
            ],
            'saldo_total' => [
                'valor' => $billetera->saldo_total,
                'formateado' => '$' . number_format($billetera->saldo_total, 0, ',', '.'),
                'titulo' => 'Patrimonio Total',
                'icono' => 'fas fa-piggy-bank',
                'color' => 'dark',
                'descripcion' => 'Disponible + Invertido + Bloqueado',
            ],
        ];
    }

    /**
     * Agregar fondos (depósito)
     */
    public function addFunds(
        Billetera $billetera,
        float $monto,
        string $tipo,
        string $descripcion,
        ?Model $referencia = null
    ): TransaccionBilletera {
        return DB::transaction(function () use ($billetera, $monto, $tipo, $descripcion, $referencia) {
            $saldoAnterior = $billetera->saldo_disponible;

            // Actualizar saldo
            $billetera->increment('saldo_disponible', $monto);

            // Registrar transacción
            return $this->createTransaction(
                $billetera,
                $tipo,
                $monto,
                'credito',
                $saldoAnterior,
                $billetera->fresh()->saldo_disponible,
                $descripcion,
                $referencia
            );
        });
    }

    /**
     * Deducir fondos (retiro, inversión)
     */
    public function deductFunds(
        Billetera $billetera,
        float $monto,
        string $tipo,
        string $descripcion,
        ?Model $referencia = null
    ): TransaccionBilletera {
        // Validar saldo suficiente
        if ($billetera->saldo_disponible < $monto) {
            throw new \Exception('Saldo insuficiente');
        }

        return DB::transaction(function () use ($billetera, $monto, $tipo, $descripcion, $referencia) {
            $saldoAnterior = $billetera->saldo_disponible;

            // Actualizar saldo
            $billetera->decrement('saldo_disponible', $monto);

            // Registrar transacción
            return $this->createTransaction(
                $billetera,
                $tipo,
                $monto,
                'debito',
                $saldoAnterior,
                $billetera->fresh()->saldo_disponible,
                $descripcion,
                $referencia
            );
        });
    }

    /**
     * Bloquear fondos (retiro pendiente)
     */
    public function blockFunds(Billetera $billetera, float $monto): void
    {
        if ($billetera->saldo_disponible < $monto) {
            throw new \Exception('Saldo insuficiente para bloquear');
        }

        DB::transaction(function () use ($billetera, $monto) {
            $billetera->decrement('saldo_disponible', $monto);
            $billetera->increment('saldo_bloqueado', $monto);
        });
    }

    /**
     * Desbloquear fondos (cancelar retiro)
     */
    public function unblockFunds(Billetera $billetera, float $monto): void
    {
        if ($billetera->saldo_bloqueado < $monto) {
            throw new \Exception('Monto bloqueado insuficiente');
        }

        DB::transaction(function () use ($billetera, $monto) {
            $billetera->decrement('saldo_bloqueado', $monto);
            $billetera->increment('saldo_disponible', $monto);
        });
    }

    /**
     * Mover fondos de disponible a invertido
     */
    public function investFunds(Billetera $billetera, float $monto): void
    {
        if ($billetera->saldo_disponible < $monto) {
            throw new \Exception('Saldo insuficiente para invertir');
        }

        DB::transaction(function () use ($billetera, $monto) {
            $billetera->decrement('saldo_disponible', $monto);
            $billetera->increment('saldo_invertido', $monto);
        });
    }

    /**
     * Registrar retorno de capital (de invertido a disponible)
     */
    public function returnCapital(Billetera $billetera, float $monto): void
    {
        DB::transaction(function () use ($billetera, $monto) {
            $billetera->decrement('saldo_invertido', $monto);
            $billetera->increment('saldo_disponible', $monto);
        });
    }

    /**
     * Registrar dividendo recibido
     */
    public function receiveDividend(
        Billetera $billetera,
        float $monto,
        string $descripcion,
        ?Model $referencia = null
    ): TransaccionBilletera {
        return DB::transaction(function () use ($billetera, $monto, $descripcion, $referencia) {
            $saldoAnterior = $billetera->saldo_disponible;

            $billetera->increment('saldo_disponible', $monto);
            $billetera->increment('retornos_acumulados', $monto);

            return $this->createTransaction(
                $billetera,
                'dividendo',
                $monto,
                'credito',
                $saldoAnterior,
                $billetera->fresh()->saldo_disponible,
                $descripcion,
                $referencia
            );
        });
    }

    /**
     * Generar código de transacción único
     */
    private function generateTransactionCode(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));

        return "TRX-{$date}-{$random}";
    }

    /**
     * Crear registro de transacción
     */
    private function createTransaction(
        Billetera $billetera,
        string $tipo,
        float $monto,
        string $naturaleza,
        float $saldoAnterior,
        float $saldoPosterior,
        string $descripcion,
        ?Model $referencia = null
    ): TransaccionBilletera {
        $data = [
            'codigo_transaccion' => $this->generateTransactionCode(),
            'billetera_id' => $billetera->id,
            'usuario_id' => $billetera->usuario_id,
            'tipo' => $tipo,
            'monto' => $monto,
            'naturaleza' => $naturaleza,
            'saldo_anterior' => $saldoAnterior,
            'saldo_posterior' => $saldoPosterior,
            'descripcion' => $descripcion,
            'fecha_transaccion' => now(),
        ];

        if ($referencia) {
            $data['referencia_type'] = get_class($referencia);
            $data['referencia_id'] = $referencia->id;
        }

        return TransaccionBilletera::create($data);
    }

    /**
     * Obtener historial de transacciones paginado
     */
    public function getTransactionHistory(User $user, array $filters = []): LengthAwarePaginator
    {
        $billetera = $this->getOrCreateWallet($user);

        $query = TransaccionBilletera::where('billetera_id', $billetera->id)
            ->orderBy('created_at', 'desc');

        // Filtro por tipo
        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        // Filtro por naturaleza (crédito/débito)
        if (!empty($filters['naturaleza'])) {
            $query->where('naturaleza', $filters['naturaleza']);
        }

        // Filtro por fecha desde
        if (!empty($filters['fecha_desde'])) {
            $query->whereDate('fecha_transaccion', '>=', $filters['fecha_desde']);
        }

        // Filtro por fecha hasta
        if (!empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha_transaccion', '<=', $filters['fecha_hasta']);
        }

        $limit = $filters['limit'] ?? 15;

        return $query->paginate($limit);
    }

    /**
     * Obtener transacciones recientes (para dashboard)
     */
    public function getRecentTransactions(User $user, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $billetera = $this->getOrCreateWallet($user);

        return TransaccionBilletera::where('billetera_id', $billetera->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Colores por tipo de transacción
     */
    public static function getTransactionTypeColor(string $tipo): string
    {
        $colores = [
            'deposito' => 'success',
            'retiro' => 'danger',
            'inversion' => 'primary',
            'dividendo' => 'warning',
            'retorno_capital' => 'info',
            'venta_trading' => 'success',
            'compra_trading' => 'primary',
            'comision' => 'secondary',
            'reversa' => 'dark',
            'ajuste' => 'secondary',
        ];

        return $colores[$tipo] ?? 'secondary';
    }

    /**
     * Obtener etiqueta legible del tipo
     */
    public static function getTransactionTypeLabel(string $tipo): string
    {
        $labels = [
            'deposito' => 'Depósito',
            'retiro' => 'Retiro',
            'inversion' => 'Inversión',
            'dividendo' => 'Dividendo',
            'retorno_capital' => 'Retorno Capital',
            'venta_trading' => 'Venta Trading',
            'compra_trading' => 'Compra Trading',
            'comision' => 'Comisión',
            'reversa' => 'Reversa',
            'ajuste' => 'Ajuste',
        ];

        return $labels[$tipo] ?? ucfirst($tipo);
    }
}
