<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Compra;
use App\Models\Comision;
use App\Models\TransaccionPago;
use App\Models\Membresia;
use App\Models\PlanMembresia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    /**
     * Dashboard principal del administrador
     */
    public function index(Request $request)
    {
        // Verificar que sea admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Filtros de fecha
        $fechaInicio = $request->get('fecha_inicio') 
            ? Carbon::parse($request->get('fecha_inicio'))->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $fechaFin = $request->get('fecha_fin') 
            ? Carbon::parse($request->get('fecha_fin'))->endOfDay() 
            : Carbon::now()->endOfDay();

        // Estadísticas generales
        $stats = $this->obtenerEstadisticasGenerales($fechaInicio, $fechaFin);
        
        // Ventas por empresa
        $ventasPorEmpresa = $this->obtenerVentasPorEmpresa($fechaInicio, $fechaFin);
        
        // Gráfico de ventas diarias
        $ventasDiarias = $this->obtenerVentasDiarias($fechaInicio, $fechaFin);
        
        // Comisiones pendientes de pago
        $comisionesPendientes = $this->obtenerComisionesPendientes();

        return view('admin.dashboard', compact(
            'stats',
            'ventasPorEmpresa',
            'ventasDiarias',
            'comisionesPendientes',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Obtener estadísticas generales
     */
    private function obtenerEstadisticasGenerales($fechaInicio, $fechaFin)
    {
        $ventasTotales = Compra::where('estado', 'pagada')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->sum('total');

        $numeroVentas = Compra::where('estado', 'pagada')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();

        $comisionesTotales = Comision::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->sum('monto_comision');

        $comisionesPendientes = Comision::where('estado', 'pendiente')
            ->sum('monto_comision');

        $totalParaEmpresas = Comision::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->sum('monto_empresa');

        return [
            'ventas_totales' => $ventasTotales,
            'numero_ventas' => $numeroVentas,
            'ticket_promedio' => $numeroVentas > 0 ? $ventasTotales / $numeroVentas : 0,
            'comisiones_totales' => $comisionesTotales,
            'comisiones_pendientes' => $comisionesPendientes,
            'total_para_empresas' => $totalParaEmpresas
        ];
    }

    /**
     * Obtener ventas por empresa
     */
    private function obtenerVentasPorEmpresa($fechaInicio, $fechaFin)
    {
        return DB::table('empresas')
            ->leftJoin('planes_membresia', 'empresas.plan_membresia_id', '=', 'planes_membresia.id')
            ->leftJoin('compras', function($join) use ($fechaInicio, $fechaFin) {
                $join->on('empresas.id', '=', 'compras.empresa_id')
                     ->where('compras.estado', '=', 'pagada')
                     ->whereBetween('compras.created_at', [$fechaInicio, $fechaFin]);
            })
            ->leftJoin('comisiones', 'compras.id', '=', 'comisiones.compra_id')
            ->select(
                'empresas.id',
                'empresas.nombre',
                'planes_membresia.porcentaje_comision',
                'planes_membresia.comision_fija',
                DB::raw('COUNT(DISTINCT compras.id) as numero_ventas'),
                DB::raw('COALESCE(SUM(compras.total), 0) as ventas_totales'),
                DB::raw('COALESCE(SUM(comisiones.monto_comision), 0) as comisiones_totales'),
                DB::raw('COALESCE(SUM(comisiones.monto_empresa), 0) as total_empresa'),
                DB::raw('COALESCE(SUM(CASE WHEN comisiones.estado = "pendiente" THEN comisiones.monto_empresa ELSE 0 END), 0) as pendiente_pagar')
            )
            ->groupBy('empresas.id', 'empresas.nombre', 'planes_membresia.porcentaje_comision', 'planes_membresia.comision_fija')
            ->orderByDesc('ventas_totales')
            ->get();
    }

    /**
     * Obtener ventas diarias para gráfico
     */
    private function obtenerVentasDiarias($fechaInicio, $fechaFin)
    {
        return Compra::where('estado', 'pagada')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as numero_ventas, SUM(total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(function($item) {
                return [
                    'fecha' => Carbon::parse($item->fecha)->format('d/m'),
                    'ventas' => $item->numero_ventas,
                    'total' => $item->total
                ];
            });
    }

    /**
     * Obtener comisiones pendientes de pago agrupadas
     */
    private function obtenerComisionesPendientes()
    {
        return DB::table('comisiones')
            ->join('empresas', 'comisiones.empresa_id', '=', 'empresas.id')
            ->where('comisiones.estado', 'pendiente')
            ->select(
                'empresas.id',
                'empresas.nombre',
                DB::raw('COUNT(comisiones.id) as numero_comisiones'),
                DB::raw('SUM(comisiones.monto_empresa) as total_pagar'),
                DB::raw('MIN(comisiones.created_at) as primera_comision'),
                DB::raw('MAX(comisiones.created_at) as ultima_comision')
            )
            ->groupBy('empresas.id', 'empresas.nombre')
            ->orderByDesc('total_pagar')
            ->get();
    }

    /**
     * Detalle de comisiones de una empresa
     */
    public function detalleEmpresa($empresaId, Request $request)
    {
        $empresa = Empresa::findOrFail($empresaId);
        
        // Filtros
        $fechaInicio = $request->get('fecha_inicio') 
            ? Carbon::parse($request->get('fecha_inicio'))->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $fechaFin = $request->get('fecha_fin') 
            ? Carbon::parse($request->get('fecha_fin'))->endOfDay() 
            : Carbon::now()->endOfDay();

        // Comisiones
        $comisiones = Comision::with(['compra.items'])
            ->where('empresa_id', $empresaId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderByDesc('created_at')
            ->paginate(20);

        // Resumen
        $resumen = [
            'ventas_totales' => $comisiones->sum('monto_venta'),
            'comisiones_totales' => $comisiones->sum('monto_comision'),
            'total_empresa' => $comisiones->sum('monto_empresa'),
            'pendiente_pagar' => Comision::where('empresa_id', $empresaId)
                ->where('estado', 'pendiente')
                ->sum('monto_empresa')
        ];

        return view('admin.detalle-empresa', compact(
            'empresa',
            'comisiones',
            'resumen',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Marcar comisiones como pagadas
     */
    public function marcarComoPagadas(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'referencia_pago' => 'required|string',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();
        
        try {
            // Obtener todas las comisiones pendientes de la empresa
            $comisiones = Comision::where('empresa_id', $request->empresa_id)
                ->where('estado', 'pendiente')
                ->get();

            if ($comisiones->isEmpty()) {
                return back()->with('error', 'No hay comisiones pendientes para esta empresa');
            }

            $totalPagar = $comisiones->sum('monto_empresa');
            
            // Crear registro de pago
            $pago = \App\Models\PagoEmpresa::create([
                'empresa_id' => $request->empresa_id,
                'periodo' => now()->format('Y-m'),
                'total_ventas' => $comisiones->sum('monto_venta'),
                'total_comisiones' => $comisiones->sum('monto_comision'),
                'total_a_pagar' => $totalPagar,
                'estado' => 'pagado',
                'fecha_pago' => now(),
                'metodo_pago' => $request->metodo_pago ?? 'transferencia',
                'referencia_pago' => $request->referencia_pago,
                'detalle_comisiones' => $comisiones->pluck('id')->toArray(),
                'observaciones' => $request->observaciones
            ]);

            // Marcar comisiones como pagadas
            foreach ($comisiones as $comision) {
                $comision->update([
                    'estado' => 'pagada',
                    'fecha_pago' => now(),
                    'referencia_pago' => $request->referencia_pago,
                    'observaciones' => $request->observaciones
                ]);
            }

            DB::commit();

            return back()->with('success', 'Pago registrado correctamente. Total: $' . number_format($totalPagar, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Exportar reporte de comisiones
     */
    public function exportarReporte(Request $request)
    {
        // TODO: Implementar exportación a Excel
        return back()->with('info', 'Función de exportación en desarrollo');
    }

    /**
     * Dashboard de membresías
     */
    public function dashboardMembresias(Request $request)
    {
        // Verificar que sea admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        // Filtros de fecha
        $fechaInicio = $request->get('fecha_inicio') 
            ? Carbon::parse($request->get('fecha_inicio'))->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $fechaFin = $request->get('fecha_fin') 
            ? Carbon::parse($request->get('fecha_fin'))->endOfDay() 
            : Carbon::now()->endOfDay();

        // Estadísticas generales de membresías
        $statsMembresias = $this->obtenerEstadisticasMembresias($fechaInicio, $fechaFin);
        
        // Ingresos por plan de membresía
        $ingresosPorPlan = $this->obtenerIngresosPorPlan($fechaInicio, $fechaFin);
        
        // Membresías por empresa
        $membresiasPorEmpresa = $this->obtenerMembresiasPorEmpresa($fechaInicio, $fechaFin);
        
        // Gráfico de membresías mensuales
        $membresiasMensuales = $this->obtenerMembresiasMensuales($fechaInicio, $fechaFin);
        
        // Membresías por vencer en los próximos 7 días
        $membresiasPorVencer = $this->obtenerMembresiasPorVencer();

        // Resumen de renovaciones
        $resumenRenovaciones = $this->obtenerResumenRenovaciones($fechaInicio, $fechaFin);

        return view('admin.dashboard-membresias', compact(
            'statsMembresias',
            'ingresosPorPlan',
            'membresiasPorEmpresa',
            'membresiasMensuales',
            'membresiasPorVencer',
            'resumenRenovaciones',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Obtener estadísticas generales de membresías
     */
    private function obtenerEstadisticasMembresias($fechaInicio, $fechaFin)
    {
        $ingresosTotal = Membresia::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->sum('precio_pagado');

        $membresiasTotales = Membresia::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->count();

        $membresiasPagadas = Membresia::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'activa')
            ->count();

        $membresiasPendientes = Membresia::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'pendiente')
            ->count();

        $membresiasCanceladas = Membresia::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('estado', 'cancelada')
            ->count();

        $membresiaActivas = Membresia::activas()->count();

        $promedioIngresosPorMembresia = $membresiasTotales > 0 ? $ingresosTotal / $membresiasTotales : 0;

        // Ingresos del mes anterior para comparación
        $mesAnteriorInicio = Carbon::parse($fechaInicio)->subMonth()->startOfMonth();
        $mesAnteriorFin = Carbon::parse($fechaInicio)->subMonth()->endOfMonth();
        $ingresosMesAnterior = Membresia::whereBetween('created_at', [$mesAnteriorInicio, $mesAnteriorFin])
            ->sum('precio_pagado');

        $crecimientoPorcentual = $ingresosMesAnterior > 0 
            ? (($ingresosTotal - $ingresosMesAnterior) / $ingresosMesAnterior) * 100 
            : 0;

        return [
            'ingresos_total' => $ingresosTotal,
            'membresias_totales' => $membresiasTotales,
            'membresias_pagadas' => $membresiasPagadas,
            'membresias_pendientes' => $membresiasPendientes,
            'membresias_canceladas' => $membresiasCanceladas,
            'membresias_activas' => $membresiaActivas,
            'promedio_ingresos_membresia' => $promedioIngresosPorMembresia,
            'crecimiento_porcentual' => $crecimientoPorcentual
        ];
    }

    /**
     * Obtener ingresos por plan de membresía
     */
    private function obtenerIngresosPorPlan($fechaInicio, $fechaFin)
    {
        return DB::table('membresias')
            ->join('planes_membresia', 'membresias.plan_membresia_id', '=', 'planes_membresia.id')
            ->whereBetween('membresias.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'planes_membresia.id',
                'planes_membresia.nombre',
                'planes_membresia.precio',
                DB::raw('COUNT(membresias.id) as total_membresias'),
                DB::raw('SUM(membresias.precio_pagado) as ingresos_total'),
                DB::raw('COUNT(CASE WHEN membresias.estado = "activa" THEN 1 END) as membresias_activas'),
                DB::raw('COUNT(CASE WHEN membresias.estado = "pendiente" THEN 1 END) as membresias_pendientes'),
                DB::raw('COUNT(CASE WHEN membresias.estado = "cancelada" THEN 1 END) as membresias_canceladas')
            )
            ->groupBy('planes_membresia.id', 'planes_membresia.nombre', 'planes_membresia.precio')
            ->orderByDesc('ingresos_total')
            ->get();
    }

    /**
     * Obtener membresías por empresa
     */
    private function obtenerMembresiasPorEmpresa($fechaInicio, $fechaFin)
    {
        return DB::table('empresas')
            ->leftJoin('membresias', function($join) use ($fechaInicio, $fechaFin) {
                $join->on('empresas.id', '=', 'membresias.empresa_id')
                     ->whereBetween('membresias.created_at', [$fechaInicio, $fechaFin]);
            })
            ->leftJoin('planes_membresia', 'membresias.plan_membresia_id', '=', 'planes_membresia.id')
            ->select(
                'empresas.id',
                'empresas.nombre',
                'empresas.email',
                DB::raw('COUNT(membresias.id) as total_membresias'),
                DB::raw('SUM(membresias.precio_pagado) as total_pagado'),
                DB::raw('MAX(membresias.created_at) as ultima_membresia'),
                DB::raw('GROUP_CONCAT(DISTINCT planes_membresia.nombre SEPARATOR ", ") as planes_contratados'),
                DB::raw('COUNT(CASE WHEN membresias.estado = "activa" THEN 1 END) as membresias_activas'),
                DB::raw('MAX(CASE WHEN membresias.estado = "activa" THEN membresias.fecha_fin END) as fecha_vencimiento')
            )
            ->groupBy('empresas.id', 'empresas.nombre', 'empresas.email')
            ->orderByDesc('total_pagado')
            ->get();
    }

    /**
     * Obtener membresías mensuales para gráfico
     */
    private function obtenerMembresiasMensuales($fechaInicio, $fechaFin)
    {
        return Membresia::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as periodo, COUNT(*) as total_membresias, SUM(precio_pagado) as ingresos')
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->map(function($item) {
                return [
                    'periodo' => Carbon::parse($item->periodo . '-01')->format('M Y'),
                    'membresias' => $item->total_membresias,
                    'ingresos' => $item->ingresos
                ];
            });
    }

    /**
     * Obtener membresías por vencer
     */
    private function obtenerMembresiasPorVencer()
    {
        return DB::table('membresias')
            ->join('empresas', 'membresias.empresa_id', '=', 'empresas.id')
            ->join('planes_membresia', 'membresias.plan_membresia_id', '=', 'planes_membresia.id')
            ->where('membresias.estado', 'activa')
            ->whereBetween('membresias.fecha_fin', [now(), now()->addDays(30)])
            ->select(
                'empresas.nombre as empresa_nombre',
                'empresas.email',
                'planes_membresia.nombre as plan_nombre',
                'membresias.fecha_fin',
                'membresias.precio_pagado',
                DB::raw('DATEDIFF(membresias.fecha_fin, NOW()) as dias_restantes')
            )
            ->orderBy('membresias.fecha_fin')
            ->get();
    }

    /**
     * Obtener resumen de renovaciones
     */
    private function obtenerResumenRenovaciones($fechaInicio, $fechaFin)
    {
        // Contar empresas que renovaron vs empresas que no renovaron
        $empresasConMembresias = Membresia::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->distinct('empresa_id')
            ->count('empresa_id');

        $renovaciones = DB::table('membresias as m1')
            ->join('membresias as m2', function($join) {
                $join->on('m1.empresa_id', '=', 'm2.empresa_id')
                     ->where('m2.created_at', '>', DB::raw('m1.created_at'));
            })
            ->whereBetween('m2.created_at', [$fechaInicio, $fechaFin])
            ->distinct('m1.empresa_id')
            ->count();

        $tasaRenovacion = $empresasConMembresias > 0 ? ($renovaciones / $empresasConMembresias) * 100 : 0;

        return [
            'empresas_con_membresias' => $empresasConMembresias,
            'renovaciones' => $renovaciones,
            'tasa_renovacion' => $tasaRenovacion
        ];
    }

    /**
     * Exportar reporte de membresías
     */
    public function exportarReporteMembresias(Request $request)
    {
        // TODO: Implementar exportación a Excel
        return back()->with('info', 'Función de exportación en desarrollo');
    }
}