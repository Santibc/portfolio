<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarkWithdrawalPaidRequest;
use App\Http\Requests\Admin\RejectWithdrawalRequest;
use App\Models\Retiro;
use App\Services\Withdrawal\WithdrawalService;
use Illuminate\Http\Request;

class WithdrawalManagementController extends Controller
{
    public function __construct(private WithdrawalService $withdrawalService)
    {
    }

    /**
     * Dashboard de retiros con estadísticas
     */
    public function index(Request $request)
    {
        $stats = $this->withdrawalService->getStats();

        $query = Retiro::with(['usuario', 'aprobadoPor', 'pagadoPor'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_solicitud', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_solicitud', '<=', $request->fecha_hasta);
        }

        if ($request->filled('usuario')) {
            $query->whereHas('usuario', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->usuario}%")
                    ->orWhere('email', 'like', "%{$request->usuario}%");
            });
        }

        $retiros = $query->paginate(15)->withQueryString();

        return view('admin.withdrawals.index', compact('stats', 'retiros'));
    }

    /**
     * Lista de retiros pendientes de aprobación
     */
    public function pending()
    {
        $retiros = Retiro::with('usuario')
            ->whereIn('estado', ['pendiente', 'en_revision'])
            ->orderBy('fecha_solicitud', 'asc')
            ->paginate(15);

        return view('admin.withdrawals.pending', compact('retiros'));
    }

    /**
     * Lista de retiros aprobados pendientes de pago
     */
    public function approved()
    {
        $retiros = Retiro::with(['usuario', 'aprobadoPor'])
            ->where('estado', 'aprobado')
            ->orderBy('fecha_aprobacion', 'asc')
            ->paginate(15);

        $totalMonto = $retiros->sum('monto_solicitado');

        return view('admin.withdrawals.approved', compact('retiros', 'totalMonto'));
    }

    /**
     * Ver detalle de retiro
     */
    public function show(Retiro $retiro)
    {
        $retiro->load(['usuario', 'aprobadoPor', 'pagadoPor']);

        // Decodificar datos de pago
        $datosPago = json_decode($retiro->datos_pago, true) ?? [];

        // URL del comprobante
        $comprobanteUrl = $this->withdrawalService->getProofUrl($retiro);

        // Timeline
        $timeline = $this->buildTimeline($retiro);

        return view('admin.withdrawals.show', compact('retiro', 'datosPago', 'comprobanteUrl', 'timeline'));
    }

    /**
     * Aprobar solicitud de retiro
     */
    public function approve(Retiro $retiro)
    {
        try {
            $this->withdrawalService->approveWithdrawal($retiro, auth()->user());

            return redirect()
                ->route('admin.withdrawals.show', $retiro)
                ->with('success', "Retiro {$retiro->codigo_retiro} aprobado exitosamente.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Rechazar solicitud de retiro
     */
    public function reject(Retiro $retiro, RejectWithdrawalRequest $request)
    {
        try {
            $this->withdrawalService->rejectWithdrawal(
                $retiro,
                auth()->user(),
                $request->input('motivo_rechazo')
            );

            return redirect()
                ->route('admin.withdrawals.pending')
                ->with('success', "Retiro {$retiro->codigo_retiro} rechazado. El saldo ha sido devuelto al usuario.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Marcar retiro como pagado
     */
    public function markAsPaid(Retiro $retiro, MarkWithdrawalPaidRequest $request)
    {
        try {
            $this->withdrawalService->markAsPaid(
                $retiro,
                auth()->user(),
                $request->file('comprobante'),
                $request->input('notas_aprobacion')
            );

            return redirect()
                ->route('admin.withdrawals.show', $retiro)
                ->with('success', "Retiro {$retiro->codigo_retiro} marcado como pagado exitosamente.");
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
            'descripcion' => "Usuario: {$retiro->usuario->name}",
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
                'descripcion' => "Por: {$retiro->aprobadoPor?->name}",
                'fecha' => $retiro->fecha_aprobacion,
                'completado' => true,
                'icono' => 'fas fa-check-circle',
                'color' => 'success',
            ];
        }

        // Rechazado
        if ($retiro->estado === 'rechazado') {
            $timeline[] = [
                'estado' => 'rechazado',
                'titulo' => 'Rechazado',
                'descripcion' => "Motivo: {$retiro->motivo_rechazo}",
                'fecha' => $retiro->fecha_rechazo,
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
                'descripcion' => "Por: {$retiro->pagadoPor?->name}",
                'fecha' => $retiro->fecha_pago,
                'completado' => true,
                'icono' => 'fas fa-money-bill-wave',
                'color' => 'success',
            ];
        }

        return $timeline;
    }
}
