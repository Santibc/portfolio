<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\ItemCompra;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAnaliticoController extends Controller
{
    public function index(Request $request)
    {
        $empresa = auth()->user()->empresa;
        $inicioMes = Carbon::now()->startOfMonth();

        // Fechas para filtros
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();
        $inicioAnio = Carbon::now()->startOfYear();

        // ============================================
        // ESTADÍSTICAS DE VENTAS
        // ============================================

        // Ventas del día
        $ventasHoy = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->whereDate('created_at', $hoy)
            ->sum('total');

        $pedidosHoy = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->whereDate('created_at', $hoy)
            ->count();

        // Ventas de la semana
        $ventasSemana = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $inicioSemana)
            ->sum('total');

        $pedidosSemana = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $inicioSemana)
            ->count();

        // Ventas del mes
        $ventasMes = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $inicioMes)
            ->sum('total');

        $pedidosMes = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $inicioMes)
            ->count();

        // Ventas del año
        $ventasAnio = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $inicioAnio)
            ->sum('total');

        // Ticket promedio del mes
        $ticketPromedio = $pedidosMes > 0 ? $ventasMes / $pedidosMes : 0;

        // ============================================
        // COMPARATIVAS CON PERÍODO ANTERIOR
        // ============================================

        // Comparativa día anterior
        $ventasAyer = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->whereDate('created_at', $hoy->copy()->subDay())
            ->sum('total');

        $cambioVentasDia = $ventasAyer > 0
            ? (($ventasHoy - $ventasAyer) / $ventasAyer) * 100
            : ($ventasHoy > 0 ? 100 : 0);

        // Comparativa semana anterior
        $ventasSemanaAnterior = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->whereBetween('created_at', [
                $inicioSemana->copy()->subWeek(),
                $inicioSemana
            ])
            ->sum('total');

        $cambioVentasSemana = $ventasSemanaAnterior > 0
            ? (($ventasSemana - $ventasSemanaAnterior) / $ventasSemanaAnterior) * 100
            : ($ventasSemana > 0 ? 100 : 0);

        // Comparativa mes anterior
        $ventasMesAnterior = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->whereBetween('created_at', [
                $inicioMes->copy()->subMonth(),
                $inicioMes
            ])
            ->sum('total');

        $cambioVentasMes = $ventasMesAnterior > 0
            ? (($ventasMes - $ventasMesAnterior) / $ventasMesAnterior) * 100
            : ($ventasMes > 0 ? 100 : 0);

        // ============================================
        // PRODUCTOS MÁS VENDIDOS
        // ============================================

        $productosMasVendidos = ItemCompra::select(
                'producto_id',
                'nombre_producto',
                DB::raw('SUM(cantidad) as total_vendido'),
                DB::raw('SUM(precio_total) as ingresos_totales'),
                DB::raw('AVG(precio_unitario) as precio_promedio')
            )
            ->whereHas('compra', function($q) use ($empresa, $inicioMes) {
                $q->where('empresa_id', $empresa->id)
                  ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
                  ->where('created_at', '>=', $inicioMes);
            })
            ->groupBy('producto_id', 'nombre_producto')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // ============================================
        // DATOS PARA GRÁFICO DE VENTAS (Últimos 30 días)
        // ============================================

        $ventasUltimos30Dias = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(total) as total_ventas'),
                DB::raw('COUNT(*) as total_pedidos')
            )
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Llenar días sin ventas con 0
        $diasGrafico = collect();
        for ($i = 29; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i)->format('Y-m-d');
            $venta = $ventasUltimos30Dias->firstWhere('fecha', $fecha);
            $diasGrafico->push([
                'fecha' => Carbon::parse($fecha)->format('d M'),
                'ventas' => $venta ? (float)$venta->total_ventas : 0,
                'pedidos' => $venta ? $venta->total_pedidos : 0,
            ]);
        }

        // ============================================
        // DATOS PARA GRÁFICO DE VENTAS POR MES (Último año)
        // ============================================

        $ventasPorMes = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->select(
                DB::raw('YEAR(created_at) as anio'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('SUM(total) as total_ventas'),
                DB::raw('COUNT(*) as total_pedidos')
            )
            ->groupBy('anio', 'mes')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        $mesesGrafico = collect();
        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $venta = $ventasPorMes->first(function($v) use ($fecha) {
                return $v->anio == $fecha->year && $v->mes == $fecha->month;
            });
            $mesesGrafico->push([
                'mes' => $fecha->translatedFormat('M Y'),
                'ventas' => $venta ? (float)$venta->total_ventas : 0,
                'pedidos' => $venta ? $venta->total_pedidos : 0,
            ]);
        }

        // ============================================
        // ESTADOS DE PEDIDOS
        // ============================================

        $pedidosPorEstado = Compra::where('empresa_id', $empresa->id)
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // ============================================
        // ENTREGAS PENDIENTES HOY
        // ============================================

        $entregasPendientesHoy = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada'])
            ->whereDate('fecha_entrega_deseada', $hoy)
            ->count();

        $entregasAtrasadas = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada'])
            ->whereDate('fecha_entrega_deseada', '<', $hoy)
            ->count();

        // ============================================
        // CLIENTES
        // ============================================

        $clientesUnicos = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->distinct('email_cliente')
            ->count('email_cliente');

        $clientesNuevosMes = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $inicioMes)
            ->whereNotIn('email_cliente', function($query) use ($empresa, $inicioMes) {
                $query->select('email_cliente')
                    ->from('compras')
                    ->where('empresa_id', $empresa->id)
                    ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
                    ->where('created_at', '<', $inicioMes);
            })
            ->distinct('email_cliente')
            ->count('email_cliente');

        // ============================================
        // HORARIOS PICO DE PEDIDOS
        // ============================================

        $pedidosPorHora = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $inicioMes)
            ->select(DB::raw('HOUR(created_at) as hora'), DB::raw('COUNT(*) as total'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->pluck('total', 'hora')
            ->toArray();

        // Llenar horas vacías
        $horasCompletas = [];
        for ($h = 0; $h < 24; $h++) {
            $horasCompletas[$h] = $pedidosPorHora[$h] ?? 0;
        }

        // ============================================
        // ESTADÍSTICAS DE STOCK
        // ============================================

        $totalProductos = StockProducto::whereHas('producto', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id)->noEliminados();
        })->count();

        // Stock real = cantidad_disponible - cantidad_reservada
        $productosConStock = StockProducto::whereHas('producto', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id)->noEliminados();
        })->whereRaw('(cantidad_disponible - cantidad_reservada) > 0')->count();

        $productosStockBajo = StockProducto::whereHas('producto', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id)->noEliminados();
        })->whereRaw('(cantidad_disponible - cantidad_reservada) <= stock_minimo')
          ->whereRaw('(cantidad_disponible - cantidad_reservada) > 0')
          ->where('alerta_stock_bajo', true)->count();

        $productosSinStock = StockProducto::whereHas('producto', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id)->noEliminados();
        })->whereRaw('(cantidad_disponible - cantidad_reservada) <= 0')->count();

        // Movimientos del mes
        $entradasMes = MovimientoStock::whereHas('producto', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->where('tipo_movimiento', 'entrada')
          ->where('created_at', '>=', $inicioMes)
          ->sum('cantidad');

        $salidasMes = MovimientoStock::whereHas('producto', function($q) use ($empresa) {
            $q->where('empresa_id', $empresa->id);
        })->where('tipo_movimiento', 'salida')
          ->where('created_at', '>=', $inicioMes)
          ->sum('cantidad');

        // Productos críticos (stock bajo o sin stock)
        $productosCriticos = StockProducto::with(['producto', 'variante'])
            ->whereHas('producto', function($q) use ($empresa) {
                $q->where('empresa_id', $empresa->id)->noEliminados();
            })
            ->where(function($q) {
                $q->whereRaw('(cantidad_disponible - cantidad_reservada) <= stock_minimo')
                  ->orWhereRaw('(cantidad_disponible - cantidad_reservada) <= 0');
            })
            ->orderByRaw('(cantidad_disponible - cantidad_reservada)')
            ->limit(10)
            ->get();

        return view('dashboard-analitico.index', compact(
            'ventasHoy', 'pedidosHoy', 'cambioVentasDia',
            'ventasSemana', 'pedidosSemana', 'cambioVentasSemana',
            'ventasMes', 'pedidosMes', 'cambioVentasMes',
            'ventasAnio', 'ticketPromedio',
            'productosMasVendidos',
            'diasGrafico', 'mesesGrafico',
            'pedidosPorEstado',
            'entregasPendientesHoy', 'entregasAtrasadas',
            'clientesUnicos', 'clientesNuevosMes',
            'horasCompletas',
            // Stock
            'totalProductos', 'productosConStock', 'productosStockBajo', 'productosSinStock',
            'entradasMes', 'salidasMes', 'productosCriticos'
        ));
    }

    /**
     * API para obtener datos de ventas filtrados
     */
    public function ventasPorPeriodo(Request $request)
    {
        $empresa = auth()->user()->empresa;
        $periodo = $request->get('periodo', 'mes');

        $fechaInicio = match($periodo) {
            'semana' => Carbon::now()->subWeek(),
            'mes' => Carbon::now()->subMonth(),
            'trimestre' => Carbon::now()->subMonths(3),
            'anio' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        $ventas = Compra::where('empresa_id', $empresa->id)
            ->whereIn('estado', ['pagada', 'enviada', 'entregada'])
            ->where('created_at', '>=', $fechaInicio)
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(total) as total_ventas'),
                DB::raw('COUNT(*) as total_pedidos')
            )
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return response()->json($ventas);
    }
}
