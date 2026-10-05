<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ProductoMercado;
use App\Services\InventarioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventarioController extends Controller
{
    public function __construct(private InventarioService $inventario)
    {
    }

    public function index(): View
    {
        $productos = ProductoMercado::conInventario()->with('tipo')->orderBy('nombre')->get();
        $ids       = $productos->pluck('id')->all();
        $stock     = $this->inventario->stockDe($ids);
        $ultimos   = $this->inventario->ultimoMovimientoDe($ids);
        $agotados  = collect($stock)->filter(fn (float $s) => $s <= 0)->count();

        return view('inventario.index', compact('productos', 'stock', 'ultimos', 'agotados'));
    }

    public function show(Request $request, ProductoMercado $producto): View
    {
        abort_unless($producto->controla_inventario || $producto->movimientos()->exists(), 404);

        $hasta = $request->filled('hasta') ? Carbon::parse($request->input('hasta')) : today();
        $desde = $request->filled('desde') ? Carbon::parse($request->input('desde')) : today()->subDays(30);

        $kardex = $this->inventario->kardex($producto, $desde->copy()->startOfDay(), $hasta->copy()->endOfDay());
        $stock  = $this->inventario->stockDeProducto($producto->id);

        return view('inventario.show', [
            'producto'     => $producto->load('tipo'),
            'stock'        => $stock,
            'saldoInicial' => $kardex['saldoInicial'],
            'movimientos'  => $kardex['movimientos']->reverse()->values(),
            'desde'        => $desde->toDateString(),
            'hasta'        => $hasta->toDateString(),
        ]);
    }
}
