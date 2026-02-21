<?php

namespace App\Services;

use App\Models\SolicitudCotizacion;
use App\Models\VentaPdv;
use App\Models\ItemSolicitudCotizacion;
use App\Models\ItemVentaPdv;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para calcular métricas del dashboard
 * Centraliza la lógica de agregación de ventas y cotizaciones
 */
class MetricasService
{
    /**
     * Obtener resumen general de ventas del período
     * Incluye ventas PdV + cotizaciones aplicadas
     */
    public function getResumenVentas(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ?? Carbon::now()->endOfMonth();

        // Ventas por cotizaciones aplicadas
        $cotizacionesAplicadas = SolicitudCotizacion::aplicadas()
            ->whereBetween('aplicada_en', [$fechaInicio, $fechaFin])
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(monto_total), 0) as monto')
            ->first();

        // Ventas PdV
        $ventasPdv = VentaPdv::completadas()
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(total), 0) as monto')
            ->first();

        $totalVentas = ($cotizacionesAplicadas->monto ?? 0) + ($ventasPdv->monto ?? 0);
        $totalTransacciones = ($cotizacionesAplicadas->cantidad ?? 0) + ($ventasPdv->cantidad ?? 0);

        return [
            'total_ventas' => $totalVentas,
            'total_transacciones' => $totalTransacciones,
            'promedio_venta' => $totalTransacciones > 0 ? $totalVentas / $totalTransacciones : 0,
            'cotizaciones' => [
                'cantidad' => $cotizacionesAplicadas->cantidad ?? 0,
                'monto' => $cotizacionesAplicadas->monto ?? 0,
            ],
            'pdv' => [
                'cantidad' => $ventasPdv->cantidad ?? 0,
                'monto' => $ventasPdv->monto ?? 0,
            ],
            'periodo' => [
                'inicio' => $fechaInicio->format('Y-m-d'),
                'fin' => $fechaFin->format('Y-m-d'),
            ],
        ];
    }

    /**
     * Obtener cotizaciones agrupadas por estado
     */
    public function getCotizacionesPorEstado(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ?? Carbon::now()->endOfMonth();

        $cotizaciones = SolicitudCotizacion::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('
                estado,
                COUNT(*) as cantidad,
                COALESCE(SUM(monto_total), 0) as monto
            ')
            ->groupBy('estado')
            ->get()
            ->keyBy('estado');

        $totalCantidad = $cotizaciones->sum('cantidad');
        $totalMonto = $cotizaciones->sum('monto');

        $pendientes = $cotizaciones->get('pendiente');
        $aplicadas = $cotizaciones->get('aplicada');
        $rechazadas = $cotizaciones->get('rechazada');

        return [
            'pendientes' => [
                'cantidad' => $pendientes->cantidad ?? 0,
                'monto' => $pendientes->monto ?? 0,
                'porcentaje' => $totalCantidad > 0 ? round((($pendientes->cantidad ?? 0) / $totalCantidad) * 100, 1) : 0,
            ],
            'aplicadas' => [
                'cantidad' => $aplicadas->cantidad ?? 0,
                'monto' => $aplicadas->monto ?? 0,
                'porcentaje' => $totalCantidad > 0 ? round((($aplicadas->cantidad ?? 0) / $totalCantidad) * 100, 1) : 0,
            ],
            'rechazadas' => [
                'cantidad' => $rechazadas->cantidad ?? 0,
                'monto' => $rechazadas->monto ?? 0,
                'porcentaje' => $totalCantidad > 0 ? round((($rechazadas->cantidad ?? 0) / $totalCantidad) * 100, 1) : 0,
            ],
            'total' => [
                'cantidad' => $totalCantidad,
                'monto' => $totalMonto,
            ],
            'tasa_conversion' => $totalCantidad > 0
                ? round((($aplicadas->cantidad ?? 0) / $totalCantidad) * 100, 1)
                : 0,
        ];
    }

    /**
     * Obtener comparativa entre dos períodos
     */
    public function getComparativaPeriodos(
        Carbon $inicioActual,
        Carbon $finActual,
        Carbon $inicioAnterior,
        Carbon $finAnterior
    ): array {
        $actual = $this->getResumenVentas($inicioActual, $finActual);
        $anterior = $this->getResumenVentas($inicioAnterior, $finAnterior);

        // Calcular variación porcentual
        $variacionVentas = $anterior['total_ventas'] > 0
            ? round((($actual['total_ventas'] - $anterior['total_ventas']) / $anterior['total_ventas']) * 100, 1)
            : ($actual['total_ventas'] > 0 ? 100 : 0);

        $variacionTransacciones = $anterior['total_transacciones'] > 0
            ? round((($actual['total_transacciones'] - $anterior['total_transacciones']) / $anterior['total_transacciones']) * 100, 1)
            : ($actual['total_transacciones'] > 0 ? 100 : 0);

        return [
            'actual' => $actual,
            'anterior' => $anterior,
            'variacion' => [
                'ventas' => [
                    'valor' => $variacionVentas,
                    'tendencia' => $variacionVentas >= 0 ? 'up' : 'down',
                ],
                'transacciones' => [
                    'valor' => $variacionTransacciones,
                    'tendencia' => $variacionTransacciones >= 0 ? 'up' : 'down',
                ],
            ],
        ];
    }

    /**
     * Obtener ranking de vendedores por período
     */
    public function getTopVendedores(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null, int $limite = 5): array
    {
        $fechaInicio = $fechaInicio ?? Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ?? Carbon::now()->endOfMonth();

        return SolicitudCotizacion::whereBetween('solicitudes_cotizacion.created_at', [$fechaInicio, $fechaFin])
            ->join('users', 'solicitudes_cotizacion.created_by', '=', 'users.id')
            ->selectRaw('
                users.id,
                users.name as vendedor,
                COUNT(*) as total_cotizaciones,
                COALESCE(SUM(monto_total), 0) as monto_total,
                SUM(CASE WHEN estado = "aplicada" THEN 1 ELSE 0 END) as aplicadas,
                SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = "rechazada" THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN estado = "aplicada" THEN monto_total ELSE 0 END) as monto_aplicadas,
                SUM(CASE WHEN estado = "aplicada" AND estado_pago = "pagado" THEN 1 ELSE 0 END) as pagadas,
                SUM(CASE WHEN estado = "aplicada" AND estado_pago = "pagado" THEN monto_total ELSE 0 END) as monto_pagadas,
                SUM(CASE WHEN estado = "aplicada" AND stock_descontado = 1 THEN 1 ELSE 0 END) as descontadas,
                SUM(CASE WHEN estado = "aplicada" AND stock_descontado = 1 THEN monto_total ELSE 0 END) as monto_descontadas
            ')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('monto_aplicadas')
            ->limit($limite)
            ->get()
            ->map(function ($vendedor) {
                $vendedor->tasa_conversion = $vendedor->total_cotizaciones > 0
                    ? round(($vendedor->descontadas / $vendedor->total_cotizaciones) * 100, 1)
                    : 0;
                return $vendedor;
            })
            ->toArray();
    }

    /**
     * Obtener productos más vendidos
     */
    public function getTopProductos(?Carbon $fechaInicio = null, ?Carbon $fechaFin = null, int $limite = 5): array
    {
        $fechaInicio = $fechaInicio ?? Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ?? Carbon::now()->endOfMonth();

        // Productos de cotizaciones aplicadas
        $productosCotizaciones = ItemSolicitudCotizacion::join('solicitudes_cotizacion', 'items_solicitud_cotizacion.solicitud_cotizacion_id', '=', 'solicitudes_cotizacion.id')
            ->join('productos', 'items_solicitud_cotizacion.producto_id', '=', 'productos.id')
            ->where('solicitudes_cotizacion.estado', 'aplicada')
            ->whereBetween('solicitudes_cotizacion.aplicada_en', [$fechaInicio, $fechaFin])
            ->selectRaw('
                productos.id,
                productos.nombre,
                productos.referencia,
                SUM(items_solicitud_cotizacion.cantidad) as cantidad_vendida,
                SUM(items_solicitud_cotizacion.precio_total) as monto_total
            ')
            ->groupBy('productos.id', 'productos.nombre', 'productos.referencia');

        // Productos de ventas PdV
        $productosPdv = ItemVentaPdv::join('ventas_pdv', 'items_venta_pdv.venta_pdv_id', '=', 'ventas_pdv.id')
            ->join('productos', 'items_venta_pdv.producto_id', '=', 'productos.id')
            ->where('ventas_pdv.estado', 'completada')
            ->whereBetween('ventas_pdv.created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('
                productos.id,
                productos.nombre,
                productos.referencia,
                SUM(items_venta_pdv.cantidad) as cantidad_vendida,
                SUM(items_venta_pdv.total) as monto_total
            ')
            ->groupBy('productos.id', 'productos.nombre', 'productos.referencia');

        // Unir ambos resultados
        $productos = $productosCotizaciones->union($productosPdv)->get();

        // Agrupar por producto y sumar
        $productosAgrupados = $productos->groupBy('id')->map(function ($items) {
            $first = $items->first();
            return [
                'id' => $first->id,
                'nombre' => $first->nombre,
                'referencia' => $first->referencia,
                'cantidad_vendida' => $items->sum('cantidad_vendida'),
                'monto_total' => $items->sum('monto_total'),
            ];
        })->sortByDesc('cantidad_vendida')->take($limite)->values()->toArray();

        return $productosAgrupados;
    }

    /**
     * Obtener tendencia diaria de ventas
     */
    public function getTendenciaDiaria(int $dias = 30): array
    {
        $fechaInicio = Carbon::now()->subDays($dias - 1)->startOfDay();
        $fechaFin = Carbon::now()->endOfDay();

        // Cotizaciones aplicadas por día
        $cotizacionesPorDia = SolicitudCotizacion::aplicadas()
            ->whereBetween('aplicada_en', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(aplicada_en) as fecha, COALESCE(SUM(monto_total), 0) as monto')
            ->groupBy('fecha')
            ->get()
            ->keyBy('fecha');

        // Ventas PdV por día
        $ventasPdvPorDia = VentaPdv::completadas()
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COALESCE(SUM(total), 0) as monto')
            ->groupBy('fecha')
            ->get()
            ->keyBy('fecha');

        // Construir array de días con totales
        $tendencia = [];
        $fecha = $fechaInicio->copy();

        while ($fecha <= $fechaFin) {
            $fechaStr = $fecha->format('Y-m-d');
            $montoCotizaciones = $cotizacionesPorDia->get($fechaStr)->monto ?? 0;
            $montoPdv = $ventasPdvPorDia->get($fechaStr)->monto ?? 0;

            $tendencia[] = [
                'fecha' => $fechaStr,
                'fecha_corta' => $fecha->format('d/m'),
                'dia_semana' => $fecha->isoFormat('ddd'),
                'monto' => $montoCotizaciones + $montoPdv,
                'cotizaciones' => $montoCotizaciones,
                'pdv' => $montoPdv,
            ];

            $fecha->addDay();
        }

        return $tendencia;
    }

    /**
     * Obtener últimas cotizaciones
     */
    public function getUltimasCotizaciones(int $limite = 10): array
    {
        return SolicitudCotizacion::with(['cliente:id,nombre_contacto', 'createdBy:id,name'])
            ->orderByDesc('created_at')
            ->limit($limite)
            ->get()
            ->map(function ($solicitud) {
                return [
                    'id' => $solicitud->id,
                    'numero' => $solicitud->numero_solicitud,
                    'cliente' => $solicitud->cliente->nombre_contacto ?? 'N/A',
                    'vendedor' => $solicitud->createdBy->name ?? 'N/A',
                    'monto' => $solicitud->monto_total,
                    'estado' => $solicitud->estado,
                    'color_estado' => $solicitud->color_estado,
                    'fecha' => $solicitud->created_at->format('d/m/Y H:i'),
                    'hace' => $solicitud->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }

    /**
     * Obtener métricas del mes actual con comparativa
     */
    public function getMetricasMesActual(): array
    {
        $inicioMesActual = Carbon::now()->startOfMonth();
        $finMesActual = Carbon::now()->endOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonth()->startOfMonth();
        $finMesAnterior = Carbon::now()->subMonth()->endOfMonth();

        $comparativa = $this->getComparativaPeriodos(
            $inicioMesActual,
            $finMesActual,
            $inicioMesAnterior,
            $finMesAnterior
        );

        $cotizaciones = $this->getCotizacionesPorEstado($inicioMesActual, $finMesActual);
        $topVendedores = $this->getTopVendedores($inicioMesActual, $finMesActual);
        $topProductos = $this->getTopProductos($inicioMesActual, $finMesActual);
        $tendencia = $this->getTendenciaDiaria(30);
        $ultimasCotizaciones = $this->getUltimasCotizaciones(10);

        return [
            'resumen' => $comparativa['actual'],
            'comparativa' => $comparativa,
            'cotizaciones' => $cotizaciones,
            'top_vendedores' => $topVendedores,
            'top_productos' => $topProductos,
            'tendencia' => $tendencia,
            'ultimas_cotizaciones' => $ultimasCotizaciones,
            'mes_actual' => Carbon::now()->isoFormat('MMMM YYYY'),
            'mes_anterior' => Carbon::now()->subMonth()->isoFormat('MMMM YYYY'),
        ];
    }
}
