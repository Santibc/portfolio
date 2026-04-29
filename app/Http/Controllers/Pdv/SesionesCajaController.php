<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\SesionCaja;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SesionesCajaController extends Controller
{
    protected CajaService $cajaService;

    public function __construct(CajaService $cajaService)
    {
        $this->cajaService = $cajaService;
    }

    public function formAbrir($cajaId)
    {
        $caja = Caja::with('ubicacion')->findOrFail($cajaId);

        if ($caja->estaAbierta()) {
            return redirect()->route('pdv.dashboard')
                ->with('error', 'Esta caja ya tiene una sesión abierta');
        }

        return view('pdv.sesiones.abrir', compact('caja'));
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'caja_id' => 'required|exists:cajas,id',
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        $resultado = $this->cajaService->abrirSesion(
            $request->caja_id,
            auth()->id(),
            $request->monto_apertura
        );

        if (!$resultado['exito']) {
            return redirect()->back()->with('error', $resultado['mensaje']);
        }

        return redirect()->route('pdv.dashboard')
            ->with('success', $resultado['mensaje']);
    }

    public function formCerrar($sesionId)
    {
        $sesion = SesionCaja::with('caja', 'usuario')->findOrFail($sesionId);

        if (!$sesion->estaAbierta()) {
            return redirect()->route('pdv.dashboard')
                ->with('error', 'Esta sesión ya está cerrada');
        }

        $resumen = $this->cajaService->calcularResumenCierre($sesionId);

        return view('pdv.sesiones.cerrar', compact('sesion', 'resumen'));
    }

    public function cerrar(Request $request)
    {
        $request->validate([
            'sesion_id' => 'required|exists:sesiones_caja,id',
            'monto_contado' => 'required|numeric|min:0',
            'observaciones_cierre' => 'nullable|string|max:1000',
        ]);

        $resultado = $this->cajaService->cerrarSesion(
            $request->sesion_id,
            $request->monto_contado,
            $request->observaciones_cierre
        );

        if (!$resultado['exito']) {
            return redirect()->back()->with('error', $resultado['mensaje']);
        }

        return redirect()->route('pdv.sesiones.resumen', $request->sesion_id)
            ->with('success', $resultado['mensaje']);
    }

    public function historial(Request $request)
    {
        if ($request->ajax()) {
            $query = SesionCaja::with('caja', 'usuario');

            // Filter by caja for non-admin
            if (!auth()->user()->hasRole('admin')) {
                $cajasIds = Caja::where('cajero_asignado_id', auth()->id())->pluck('id');
                $query->whereIn('caja_id', $cajasIds);
            }

            if ($request->caja_id) {
                $query->where('caja_id', $request->caja_id);
            }

            return DataTables::of($query->orderByDesc('abierta_en'))
                ->addColumn('caja_nombre', fn($s) => $s->caja->nombre ?? '-')
                ->addColumn('usuario_nombre', fn($s) => $s->usuario->name ?? '-')
                ->addColumn('estado_badge', fn($s) => $s->estado === 'abierta'
                    ? '<span class="badge bg-success">Abierta</span>'
                    : '<span class="badge bg-secondary">Cerrada</span>')
                ->addColumn('diferencia_display', function ($s) {
                    if ($s->diferencia === null) return '-';
                    $color = $s->diferencia_color;
                    return '<span class="' . $color . ' fw-bold">$' . number_format($s->diferencia, 2) . '</span>';
                })
                ->addColumn('action', function ($s) {
                    $btn = '<a href="' . route('pdv.sesiones.resumen', $s->id) . '" class="btn btn-sm btn-outline-info me-1" title="Ver resumen"><i class="bi bi-eye"></i></a>';
                    if ($s->estado === 'cerrada') {
                        $btn .= '<a href="' . route('pdv.sesiones.ticket-print', $s->id) . '" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Imprimir Ticket"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['estado_badge', 'diferencia_display', 'action'])
                ->make(true);
        }

        $cajas = Caja::all();
        return view('pdv.sesiones.historial', compact('cajas'));
    }

    public function resumen($id)
    {
        $sesion = SesionCaja::with('caja', 'usuario', 'ventas.items', 'vales')->findOrFail($id);
        $resumen = $this->cajaService->calcularResumenCierre($id);

        return view('pdv.sesiones.resumen', compact('sesion', 'resumen'));
    }

    public function ticketPrintCierre($id)
    {
        $sesion = SesionCaja::with('caja.ubicacion', 'usuario', 'ventas.items', 'vales')->findOrFail($id);
        $resumen = $this->cajaService->calcularResumenCierre($id);

        return view('pdv.pdf.cierre-ticket', compact('sesion', 'resumen'));
    }
}
