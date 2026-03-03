<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MetricasService;
use App\Exports\ReporteVentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HomeController extends Controller
{
    protected MetricasService $metricasService;

    public function __construct(MetricasService $metricasService)
    {
        $this->metricasService = $metricasService;
    }

    public function index(Request $request)
    {
        // Si el usuario tiene rol cliente, redirigir al portal
        if (Auth::check() && Auth::user()->hasRole('cliente')) {
            return redirect()->route('portal.dashboard');
        }

        // Determinar el período de filtro
        $periodo = $request->get('periodo', 'mes'); // mes, semana, hoy, año

        switch ($periodo) {
            case 'hoy':
                $fechaInicio = Carbon::now()->startOfDay();
                $fechaFin = Carbon::now()->endOfDay();
                $fechaInicioAnterior = Carbon::now()->subDay()->startOfDay();
                $fechaFinAnterior = Carbon::now()->subDay()->endOfDay();
                break;
            case 'semana':
                $fechaInicio = Carbon::now()->startOfWeek();
                $fechaFin = Carbon::now()->endOfWeek();
                $fechaInicioAnterior = Carbon::now()->subWeek()->startOfWeek();
                $fechaFinAnterior = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'año':
                $fechaInicio = Carbon::now()->startOfYear();
                $fechaFin = Carbon::now()->endOfYear();
                $fechaInicioAnterior = Carbon::now()->subYear()->startOfYear();
                $fechaFinAnterior = Carbon::now()->subYear()->endOfYear();
                break;
            default: // mes
                $fechaInicio = Carbon::now()->startOfMonth();
                $fechaFin = Carbon::now()->endOfMonth();
                $fechaInicioAnterior = Carbon::now()->subMonth()->startOfMonth();
                $fechaFinAnterior = Carbon::now()->subMonth()->endOfMonth();
        }

        // Solo admin ve todas las métricas
        if (Auth::user()->hasRole('admin')) {
            $metricas = $this->metricasService->getMetricasMesActual();

            // Si hay filtro personalizado, recalcular
            if ($periodo !== 'mes') {
                $metricas['resumen'] = $this->metricasService->getResumenVentas($fechaInicio, $fechaFin);
                $metricas['comparativa'] = $this->metricasService->getComparativaPeriodos(
                    $fechaInicio,
                    $fechaFin,
                    $fechaInicioAnterior,
                    $fechaFinAnterior
                );
                $metricas['cotizaciones'] = $this->metricasService->getCotizacionesPorEstado($fechaInicio, $fechaFin);
                $metricas['top_vendedores'] = $this->metricasService->getTopVendedores($fechaInicio, $fechaFin);
                $metricas['top_productos'] = $this->metricasService->getTopProductos($fechaInicio, $fechaFin);
            }

            // Cotizaciones paginadas del período seleccionado
            $cotizacionesPaginadas = $this->metricasService->getCotizacionesPaginadas(
                $fechaInicio,
                $fechaFin,
                12
            );
            $cotizacionesPaginadas->appends(['periodo' => $periodo]);

            return view('dashboard', [
                'metricas' => $metricas,
                'periodo' => $periodo,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'cotizacionesPaginadas' => $cotizacionesPaginadas,
            ]);
        }

        // Para vendedores: vista simplificada con sus propias métricas
        return view('dashboard', [
            'metricas' => null,
            'periodo' => $periodo,
        ]);
    }

    /**
     * Exportar reporte de ventas a Excel
     */
    public function exportarVentasExcel(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio')
            ? Carbon::parse($request->get('fecha_inicio'))
            : Carbon::now()->startOfMonth();

        $fechaFin = $request->get('fecha_fin')
            ? Carbon::parse($request->get('fecha_fin'))
            : Carbon::now()->endOfMonth();

        $nombreArchivo = 'reporte-ventas-' . $fechaInicio->format('Ymd') . '-' . $fechaFin->format('Ymd') . '.xlsx';

        return Excel::download(
            new ReporteVentasExport($fechaInicio, $fechaFin),
            $nombreArchivo
        );
    }

    /**
     * Exportar métricas a PDF
     */
    public function exportarMetricasPdf(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio')
            ? Carbon::parse($request->get('fecha_inicio'))
            : Carbon::now()->startOfMonth();

        $fechaFin = $request->get('fecha_fin')
            ? Carbon::parse($request->get('fecha_fin'))
            : Carbon::now()->endOfMonth();

        $metricas = [
            'resumen' => $this->metricasService->getResumenVentas($fechaInicio, $fechaFin),
            'cotizaciones' => $this->metricasService->getCotizacionesPorEstado($fechaInicio, $fechaFin),
            'top_vendedores' => $this->metricasService->getTopVendedores($fechaInicio, $fechaFin, 10),
            'top_productos' => $this->metricasService->getTopProductos($fechaInicio, $fechaFin, 10),
        ];

        $pdf = PDF::loadView('pdf.reporte-metricas', [
            'metricas' => $metricas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ]);

        $pdf->setPaper('letter', 'portrait');

        $nombreArchivo = 'reporte-metricas-' . $fechaInicio->format('Ymd') . '-' . $fechaFin->format('Ymd') . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
