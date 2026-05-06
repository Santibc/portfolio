<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Services\ReportesPdvService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportesPdvController extends Controller
{
    protected ReportesPdvService $reportesService;

    public function __construct(ReportesPdvService $reportesService)
    {
        $this->reportesService = $reportesService;
    }

    public function index()
    {
        return view('pdv.reportes.index');
    }

    public function ventasDiarias(Request $request)
    {
        $datos = $this->reportesService->ventasDiarias(
            $request->caja_id,
            $request->fecha ?? now()->toDateString()
        );

        $cajas = Caja::all();
        return view('pdv.reportes.ventas-diarias', compact('datos', 'cajas'));
    }

    public function cierreTurno(Request $request)
    {
        $cajas = Caja::all();

        $datos = null;
        if ($request->sesion_id) {
            $datos = $this->reportesService->reporteCierre($request->sesion_id);
        }

        return view('pdv.reportes.cierre-turno', compact('datos', 'cajas'));
    }

    public function topProductos(Request $request)
    {
        $datos = $this->reportesService->topProductos(
            $request->caja_id,
            $request->fecha_desde,
            $request->fecha_hasta,
            $request->limite ?? 10
        );

        $cajas = Caja::all();
        return view('pdv.reportes.top-productos', compact('datos', 'cajas'));
    }

    public function comparativaCajas(Request $request)
    {
        $datos = null;
        if ($request->fecha_desde && $request->fecha_hasta) {
            $datos = $this->reportesService->comparativaCajas($request->fecha_desde, $request->fecha_hasta);
        }

        return view('pdv.reportes.comparativa-cajas', compact('datos'));
    }

    public function reporteVales(Request $request)
    {
        $datos = $this->reportesService->reporteVales($request->all());
        $cajas = Caja::all();
        return view('pdv.reportes.vales', compact('datos', 'cajas'));
    }

    public function reportePrefacturas(Request $request)
    {
        $datos = $this->reportesService->reportePrefacturas($request->all());
        return view('pdv.reportes.prefacturas', compact('datos'));
    }

    public function exportar(Request $request)
    {
        // Generic export handler
        $tipo = $request->tipo;
        $formato = $request->formato ?? 'pdf';

        // TODO: Implement exports
        return redirect()->back()->with('info', 'Exportación en desarrollo');
    }
}
