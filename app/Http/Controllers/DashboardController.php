<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\SolicitudCotizacion;
use App\Models\StockProducto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $esVendedor = $user && $user->hasRole('vendedor') && !$user->hasRole('admin');

        // ===== Solicitudes =====
        $solicitudesQuery = SolicitudCotizacion::query();
        if ($esVendedor) {
            $solicitudesQuery->whereHas('cliente', fn ($c) => $c->where('vendedor_id', $user->id));
        }

        $solicitudesTotal      = (clone $solicitudesQuery)->count();
        $solicitudesUltimos7   = (clone $solicitudesQuery)->where('created_at', '>=', now()->subDays(7))->count();
        $solicitudesPendientes = (clone $solicitudesQuery)->where('estado', 'pendiente')->count();

        // ===== Clientes activos =====
        $clientesQuery = Cliente::query()->where('activo', true);
        if ($esVendedor) {
            $clientesQuery->where('vendedor_id', $user->id);
        }
        $clientesActivos = $clientesQuery->count();

        // ===== Productos =====
        // Los productos no se filtran por vendedor — el catálogo es compartido.
        $productosActivos = Producto::activos()->count();

        // Productos con stock disponible vs sin stock
        // Reglas:
        //   - Si controlar_stock = false → cuenta como "disponible" (ilimitado)
        //   - Si controlar_stock = true  → mira stock_productos (suma disponible - reservada > 0)
        $productosNoControlanStock = Producto::activos()
            ->where('controlar_stock', false)
            ->count();

        // Productos que SÍ controlan stock con al menos una unidad disponible
        $productosConStockReal = Producto::activos()
            ->where('controlar_stock', true)
            ->whereHas('stock', function ($q) {
                $q->whereRaw('(cantidad_disponible - cantidad_reservada) > 0');
            })
            ->count();

        $productosConStock = $productosNoControlanStock + $productosConStockReal;
        $productosSinStock = max(0, $productosActivos - $productosConStock);

        // ===== Solicitudes por mes (últimos 12 meses) =====
        $desde = now()->startOfMonth()->subMonths(11);
        $porMes = (clone $solicitudesQuery)
            ->where('created_at', '>=', $desde)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        // Rellenar meses sin datos con 0 para que la gráfica sea continua
        $labels = [];
        $valores = [];
        for ($i = 0; $i < 12; $i++) {
            $fecha = $desde->copy()->addMonths($i);
            $key   = $fecha->format('Y-m');
            $labels[]  = $fecha->translatedFormat('M Y');
            $valores[] = (int) ($porMes[$key] ?? 0);
        }

        return view('dashboard', [
            'esVendedor'            => $esVendedor,
            'solicitudesTotal'      => $solicitudesTotal,
            'solicitudesUltimos7'   => $solicitudesUltimos7,
            'solicitudesPendientes' => $solicitudesPendientes,
            'clientesActivos'       => $clientesActivos,
            'productosActivos'      => $productosActivos,
            'productosConStock'     => $productosConStock,
            'productosSinStock'     => $productosSinStock,
            'chartLabels'           => $labels,
            'chartValores'          => $valores,
        ]);
    }
}
