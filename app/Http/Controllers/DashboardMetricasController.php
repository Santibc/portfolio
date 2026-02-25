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
            ->select(
                'created_by',
                DB::raw('COUNT(*) as total_solicitudes'),
                DB::raw('SUM(monto_total) as valor_total'),
                DB::raw('SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as total_pendientes'),
                DB::raw('SUM(CASE WHEN estado = "pendiente" THEN monto_total ELSE 0 END) as valor_pendientes'),
                DB::raw('SUM(CASE WHEN estado = "aplicada" THEN 1 ELSE 0 END) as total_aplicadas'),
                DB::raw('SUM(CASE WHEN estado = "aplicada" THEN monto_total ELSE 0 END) as valor_aplicadas'),
                DB::raw('SUM(CASE WHEN estado = "aplicada" AND estado_pago = "pagado" THEN 1 ELSE 0 END) as total_pagadas'),
                DB::raw('SUM(CASE WHEN estado = "aplicada" AND estado_pago = "pagado" THEN monto_total ELSE 0 END) as valor_pagadas'),
                DB::raw('SUM(CASE WHEN estado = "aplicada" AND stock_descontado = 1 THEN 1 ELSE 0 END) as total_descontadas'),
                DB::raw('SUM(CASE WHEN estado = "aplicada" AND stock_descontado = 1 THEN monto_total ELSE 0 END) as valor_descontadas'),
                DB::raw('SUM(CASE WHEN estado = "rechazada" THEN 1 ELSE 0 END) as total_rechazadas'),
                DB::raw('SUM(CASE WHEN estado = "rechazada" THEN monto_total ELSE 0 END) as valor_rechazadas')
            )
            ->whereNotNull('created_by')
            ->groupBy('created_by')
            ->with('createdBy:id,name')
            ->get()
            ->map(function($item) {
                return [
                    'asesor' => $item->createdBy ? $item->createdBy->name : 'Sin asignar',
                    'total_solicitudes' => $item->total_solicitudes,
                    'valor_total' => $item->valor_total,
                    'total_pendientes' => $item->total_pendientes,
                    'valor_pendientes' => $item->valor_pendientes,
                    'total_aplicadas' => $item->total_aplicadas,
                    'valor_aplicadas' => $item->valor_aplicadas,
                    'total_pagadas' => $item->total_pagadas,
                    'valor_pagadas' => $item->valor_pagadas,
                    'total_descontadas' => $item->total_descontadas,
                    'valor_descontadas' => $item->valor_descontadas,
                    'total_rechazadas' => $item->total_rechazadas,
                    'valor_rechazadas' => $item->valor_rechazadas,
                ];
            });

        // 3. Pendientes
        $solicitudesPendientes = (clone $querySolicitudes)->where('estado', 'pendiente');
        $valorPendiente = $solicitudesPendientes->sum('monto_total');
        $totalPendientes = $solicitudesPendientes->count();

        // 4. Aplicadas
        $solicitudesAplicadas = (clone $querySolicitudes)->where('estado', 'aplicada');
        $valorAplicadas = $solicitudesAplicadas->sum('monto_total');
        $totalAplicadas = $solicitudesAplicadas->count();

        // 5. Pagadas (aplicada + pagado)
        $solicitudesPagadas = (clone $querySolicitudes)->where('estado', 'aplicada')->where('estado_pago', 'pagado');
        $valorPagadas = $solicitudesPagadas->sum('monto_total');
        $totalPagadas = $solicitudesPagadas->count();

        // 6. Descontadas (aplicada + stock_descontado)
        $solicitudesDescontadas = (clone $querySolicitudes)->where('estado', 'aplicada')->where('stock_descontado', 1);
        $valorDescontadas = $solicitudesDescontadas->sum('monto_total');
        $totalDescontadas = $solicitudesDescontadas->count();

        // 7. Rechazadas
        $solicitudesRechazadas = (clone $querySolicitudes)->where('estado', 'rechazada');
        $valorRechazadas = $solicitudesRechazadas->sum('monto_total');
        $totalRechazadas = $solicitudesRechazadas->count();

        // Calcular porcentajes
        $porcentajePendientes = $totalSolicitudes > 0 ? ($totalPendientes / $totalSolicitudes) * 100 : 0;
        $porcentajeAplicadas = $totalSolicitudes > 0 ? ($totalAplicadas / $totalSolicitudes) * 100 : 0;
        $porcentajePagadas = $totalSolicitudes > 0 ? ($totalPagadas / $totalSolicitudes) * 100 : 0;
        $porcentajeDescontadas = $totalSolicitudes > 0 ? ($totalDescontadas / $totalSolicitudes) * 100 : 0;
        $porcentajeRechazadas = $totalSolicitudes > 0 ? ($totalRechazadas / $totalSolicitudes) * 100 : 0;

        return view('dashboard-metricas.index', compact(
            'valorCotizadoTotal',
            'totalSolicitudes',
            'valorPorAsesor',
            'valorPendiente',
            'totalPendientes',
            'porcentajePendientes',
            'valorAplicadas',
            'totalAplicadas',
            'porcentajeAplicadas',
            'valorPagadas',
            'totalPagadas',
            'porcentajePagadas',
            'valorDescontadas',
            'totalDescontadas',
            'porcentajeDescontadas',
            'valorRechazadas',
            'totalRechazadas',
            'porcentajeRechazadas',
            'fechaDesde',
            'fechaHasta'
        ));
    }
}
