<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCotizacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardMetricasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Verificar que el usuario sea admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }
        // Obtener filtros de fecha
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        // Query base para solicitudes
        $querySolicitudes = SolicitudCotizacion::query();

        // Aplicar filtros de fecha si existen
        if ($fechaDesde) {
            $querySolicitudes->whereDate('created_at', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $querySolicitudes->whereDate('created_at', '<=', $fechaHasta);
        }

        // 1. Valor cotizado total (todas las solicitudes)
        $valorCotizadoTotal = (clone $querySolicitudes)->sum('monto_total');
        $totalSolicitudes = (clone $querySolicitudes)->count();

        // 2. Valor cotizado por asesor comercial (usuario que creó la solicitud)
        $valorPorAsesor = (clone $querySolicitudes)
            ->select('created_by', DB::raw('COUNT(*) as total_solicitudes'), DB::raw('SUM(monto_total) as valor_total'))
            ->whereNotNull('created_by')
            ->groupBy('created_by')
            ->with('createdBy:id,name')
            ->get()
            ->map(function($item) {
                return [
                    'asesor' => $item->createdBy ? $item->createdBy->name : 'Sin asignar',
                    'total_solicitudes' => $item->total_solicitudes,
                    'valor_total' => $item->valor_total
                ];
            });

        // 3. Valor y total de cotizaciones aprobadas (aplicadas)
        $solicitudesAprobadas = (clone $querySolicitudes)->where('estado', 'aplicada');
        $valorAprobado = $solicitudesAprobadas->sum('monto_total');
        $totalAprobadas = $solicitudesAprobadas->count();

        // 4. Valor y total de cotizaciones perdidas (rechazadas)
        $solicitudesPerdidas = (clone $querySolicitudes)->where('estado', 'rechazada');
        $valorPerdido = $solicitudesPerdidas->sum('monto_total');
        $totalPerdidas = $solicitudesPerdidas->count();

        // 5. Cotizaciones pendientes
        $solicitudesPendientes = (clone $querySolicitudes)->where('estado', 'pendiente');
        $valorPendiente = $solicitudesPendientes->sum('monto_total');
        $totalPendientes = $solicitudesPendientes->count();

        // Calcular porcentajes
        $porcentajeAprobadas = $totalSolicitudes > 0 ? ($totalAprobadas / $totalSolicitudes) * 100 : 0;
        $porcentajePerdidas = $totalSolicitudes > 0 ? ($totalPerdidas / $totalSolicitudes) * 100 : 0;
        $porcentajePendientes = $totalSolicitudes > 0 ? ($totalPendientes / $totalSolicitudes) * 100 : 0;

        return view('dashboard-metricas.index', compact(
            'valorCotizadoTotal',
            'totalSolicitudes',
            'valorPorAsesor',
            'valorAprobado',
            'totalAprobadas',
            'valorPerdido',
            'totalPerdidas',
            'valorPendiente',
            'totalPendientes',
            'porcentajeAprobadas',
            'porcentajePerdidas',
            'porcentajePendientes',
            'fechaDesde',
            'fechaHasta'
        ));
    }
}
