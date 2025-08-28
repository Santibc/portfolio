<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Compra;
use App\Models\Comision;
use App\Models\TransaccionPago;
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
            ->leftJoin('compras', function($join) use ($fechaInicio, $fechaFin) {
                $join->on('empresas.id', '=', 'compras.empresa_id')
                     ->where('compras.estado', '=', 'pagada')
                     ->whereBetween('compras.created_at', [$fechaInicio, $fechaFin]);
            })
            ->leftJoin('comisiones', 'compras.id', '=', 'comisiones.compra_id')
            ->select(
                'empresas.id',
                'empresas.nombre',
                'empresas.porcentaje_comision',
                'empresas.cargo_fijo_comision', // Corregido: usar el nombre correcto del campo
                DB::raw('COUNT(DISTINCT compras.id) as numero_ventas'),
                DB::raw('COALESCE(SUM(compras.total), 0) as ventas_totales'),
                DB::raw('COALESCE(SUM(comisiones.monto_comision), 0) as comisiones_totales'),
                DB::raw('COALESCE(SUM(comisiones.monto_empresa), 0) as total_empresa'),
                DB::raw('COALESCE(SUM(CASE WHEN comisiones.estado = "pendiente" THEN comisiones.monto_empresa ELSE 0 END), 0) as pendiente_pagar')
            )
            ->groupBy('empresas.id', 'empresas.nombre', 'empresas.porcentaje_comision', 'empresas.cargo_fijo_comision')
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
}