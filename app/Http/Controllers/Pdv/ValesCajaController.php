<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\ValeCaja;
use App\Models\Caja;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class ValesCajaController extends Controller
{
    protected CajaService $cajaService;

    public function __construct(CajaService $cajaService)
    {
        $this->cajaService = $cajaService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ValeCaja::with('usuario', 'caja', 'sesionCaja');

            if (!auth()->user()->hasRole('admin')) {
                $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());
                if ($sesion) {
                    $query->where('caja_id', $sesion->caja_id);
                }
            }

            if ($request->estado) {
                $query->where('estado', $request->estado);
            }
            if ($request->caja_id) {
                $query->where('caja_id', $request->caja_id);
            }

            return DataTables::of($query->orderByDesc('created_at'))
                ->addColumn('caja_nombre', fn($v) => $v->caja->nombre ?? '-')
                ->addColumn('usuario_nombre', fn($v) => $v->usuario->name ?? '-')
                ->addColumn('estado_badge', fn($v) => $v->estado_badge)
                ->addColumn('action', function ($v) {
                    $btn = '';
                    if ($v->estado === 'pendiente') {
                        $btn .= '<button class="btn btn-sm btn-outline-success me-1" onclick="redimirVale(' . $v->id . ')" title="Redimir"><i class="bi bi-check-circle"></i></button>';
                        $btn .= '<button class="btn btn-sm btn-outline-danger" onclick="anularVale(' . $v->id . ')" title="Anular"><i class="bi bi-x-circle"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }

        $cajas = Caja::all();
        $sesionActiva = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());

        return view('pdv.vales.index', compact('cajas', 'sesionActiva'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:500',
            'monto' => 'required|numeric|min:0.01',
        ]);

        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());
        if (!$sesion) {
            return response()->json(['exito' => false, 'mensaje' => 'Debe tener una caja abierta'], 422);
        }

        $vale = ValeCaja::create([
            'sesion_caja_id' => $sesion->id,
            'caja_id' => $sesion->caja_id,
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'usuario_id' => auth()->id(),
        ]);

        return response()->json([
            'exito' => true,
            'vale' => $vale,
            'mensaje' => 'Vale creado exitosamente por $' . number_format($vale->monto, 2),
        ]);
    }

    public function redimir($id)
    {
        $vale = ValeCaja::findOrFail($id);

        if ($vale->estado !== 'pendiente') {
            return response()->json(['exito' => false, 'mensaje' => 'Solo se pueden redimir vales pendientes'], 422);
        }

        $vale->redimir(auth()->id());

        return response()->json(['exito' => true, 'mensaje' => 'Vale redimido exitosamente']);
    }

    public function anular(Request $request, $id)
    {
        $request->validate(['motivo_anulacion' => 'required|string|min:5']);

        $vale = ValeCaja::findOrFail($id);

        if ($vale->estado === 'anulado') {
            return response()->json(['exito' => false, 'mensaje' => 'Este vale ya está anulado'], 422);
        }

        $vale->anular(auth()->id(), $request->motivo_anulacion);

        return response()->json(['exito' => true, 'mensaje' => 'Vale anulado exitosamente']);
    }

    public function exportarExcel(Request $request)
    {
        // TODO: Implement Excel export using maatwebsite/excel
        return redirect()->back()->with('info', 'Exportación Excel en desarrollo');
    }

    public function exportarPdf(Request $request)
    {
        $sesion = $this->cajaService->obtenerSesionActivaDeUsuario(auth()->id());
        $query = ValeCaja::with('usuario', 'caja');

        if ($sesion) {
            $query->where('sesion_caja_id', $sesion->id);
        }

        $vales = $query->orderByDesc('created_at')->get();

        $pdf = Pdf::loadView('pdv.pdf.vales', compact('vales'));
        return $pdf->download('vales-caja.pdf');
    }
}
