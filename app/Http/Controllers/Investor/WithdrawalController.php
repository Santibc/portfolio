<?php

namespace App\Http\Controllers\Investor;

use App\DTOs\CreateWithdrawalDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\StoreWithdrawalRequest;
use App\Models\Retiro;
use App\Services\Wallet\WalletService;
use App\Services\Withdrawal\WithdrawalService;
use App\Services\Withdrawal\WithdrawalValidationService;

class WithdrawalController extends Controller
{
    public function __construct(
        private WithdrawalService $withdrawalService,
        private WithdrawalValidationService $validationService,
        private WalletService $walletService
    ) {}

    /**
     * Listado de retiros del usuario
     */
    public function index()
    {
        $user = auth()->user();
        $retiros = Retiro::where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('investor.withdrawals.index', compact('retiros'));
    }

    /**
     * Formulario para solicitar retiro
     */
    public function create()
    {
        $user = auth()->user();
        $billetera = $this->walletService->getOrCreateWallet($user);
        $summary = $this->walletService->getBalanceSummary($user);

        // Verificar si tiene retiro pendiente
        $tienePendiente = !$this->validationService->validateNoPendingWithdrawals($user);

        // Obtener límites
        $montoMinimo = $this->validationService->getMinimumAmount();
        $limiteDiario = $this->validationService->getDailyLimit();

        // Bancos disponibles
        $bancos = [
            'Bancolombia',
            'Banco de Bogotá',
            'Davivienda',
            'BBVA',
            'Banco de Occidente',
            'Banco Popular',
            'Banco AV Villas',
            'Banco Caja Social',
            'Banco Falabella',
            'Banco Finandina',
            'Banco GNB Sudameris',
            'Banco Itaú',
            'Banco Pichincha',
            'Bancoomeva',
            'Scotiabank Colpatria',
            'Citibank',
            'Otro',
        ];

        return view('investor.withdrawals.create', compact(
            'billetera',
            'summary',
            'tienePendiente',
            'montoMinimo',
            'limiteDiario',
            'bancos'
        ));
    }

    /**
     * Crear solicitud de retiro
     */
    public function store(StoreWithdrawalRequest $request)
    {
        $user = auth()->user();

        try {
            $dto = CreateWithdrawalDTO::fromRequest($request->validated(), $user->id);
            $retiro = $this->withdrawalService->createWithdrawal($user, $dto);

            return redirect()
                ->route('inversionista.withdrawals.show', $retiro)
                ->with('success', 'Solicitud de retiro creada exitosamente. Código: ' . $retiro->codigo_retiro);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Ver detalle de retiro
     */
    public function show(Retiro $retiro)
    {
        // Verificar que el retiro pertenece al usuario
        if ($retiro->usuario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este retiro.');
        }

        // Obtener URL del comprobante si existe
        $comprobanteUrl = $this->withdrawalService->getProofUrl($retiro);

        // Construir timeline
        $timeline = $this->buildTimeline($retiro);

        return view('investor.withdrawals.show', compact('retiro', 'comprobanteUrl', 'timeline'));
    }

    /**
     * Cancelar solicitud de retiro
     */
    public function cancel(Retiro $retiro)
    {
        // Verificar que el retiro pertenece al usuario
        if ($retiro->usuario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para cancelar este retiro.');
        }

        try {
            $this->withdrawalService->cancelWithdrawal($retiro);

            return redirect()
                ->route('inversionista.withdrawals.index')
                ->with('success', 'Retiro cancelado exitosamente. El saldo ha sido devuelto a tu cuenta.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Construir timeline del retiro
     */
    private function buildTimeline(Retiro $retiro): array
    {
        $timeline = [];

        // Solicitud creada
        $timeline[] = [
            'estado' => 'creado',
            'titulo' => 'Solicitud creada',
            'fecha' => $retiro->created_at,
            'completado' => true,
            'icono' => 'fas fa-plus-circle',
            'color' => 'primary',
        ];

        // Aprobado
        if ($retiro->fecha_aprobacion) {
            $timeline[] = [
                'estado' => 'aprobado',
                'titulo' => 'Aprobado',
                'fecha' => $retiro->fecha_aprobacion,
                'usuario' => $retiro->aprobadoPor?->name,
                'completado' => true,
                'icono' => 'fas fa-check-circle',
                'color' => 'success',
            ];
        } elseif ($retiro->estado === 'pendiente' || $retiro->estado === 'en_revision') {
            $timeline[] = [
                'estado' => 'pendiente_aprobacion',
                'titulo' => 'Pendiente de aprobación',
                'fecha' => null,
                'completado' => false,
                'icono' => 'fas fa-clock',
                'color' => 'warning',
            ];
        }

        // Rechazado
        if ($retiro->estado === 'rechazado') {
            $timeline[] = [
                'estado' => 'rechazado',
                'titulo' => 'Rechazado',
                'fecha' => $retiro->fecha_rechazo,
                'motivo' => $retiro->motivo_rechazo,
                'usuario' => $retiro->aprobadoPor?->name,
                'completado' => true,
                'icono' => 'fas fa-times-circle',
                'color' => 'danger',
            ];
        }

        // Cancelado
        if ($retiro->estado === 'cancelado') {
            $timeline[] = [
                'estado' => 'cancelado',
                'titulo' => 'Cancelado por el usuario',
                'fecha' => $retiro->updated_at,
                'completado' => true,
                'icono' => 'fas fa-ban',
                'color' => 'secondary',
            ];
        }

        // Pagado
        if ($retiro->fecha_pago) {
            $timeline[] = [
                'estado' => 'pagado',
                'titulo' => 'Pago realizado',
                'fecha' => $retiro->fecha_pago,
                'usuario' => $retiro->pagadoPor?->name,
                'completado' => true,
                'icono' => 'fas fa-money-bill-wave',
                'color' => 'success',
            ];
        } elseif ($retiro->estado === 'aprobado') {
            $timeline[] = [
                'estado' => 'pendiente_pago',
                'titulo' => 'Pendiente de pago',
                'fecha' => null,
                'completado' => false,
                'icono' => 'fas fa-hourglass-half',
                'color' => 'info',
            ];
        }

        return $timeline;
    }
}
