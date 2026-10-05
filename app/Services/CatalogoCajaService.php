<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Venta;
use App\Models\VentaItem;
use Illuminate\Support\Collection;

/** Arma los datos del catálogo de caja (items, componentes de inventario y stock) para el POS. */
class CatalogoCajaService
{
    public function __construct(private InventarioService $inventario)
    {
    }

    /**
     * @param  Collection<int,MenuItem>  $items
     * @return array<int,array<string,mixed>>
     */
    public function menuPayload(Collection $items): array
    {
        $items->loadMissing(['tipo', 'componentes.opciones']);

        return $items->map(fn (MenuItem $i) => [
            'id'          => $i->id,
            'nombre'      => $i->nombre,
            'precio'      => (int) $i->precio,
            'tipo_id'     => $i->tipo_id,
            'tipo'        => $i->tipo?->nombre,
            'imagen'      => $i->imagen_url ?: null,
            'componentes' => $i->componentes
                ->filter(fn ($c) => $c->opciones->isNotEmpty())
                ->map(fn ($c) => [
                    'id'       => $c->id,
                    'nombre'   => $c->nombre,
                    'cantidad' => (float) $c->cantidad,
                    'opciones' => $c->opciones->map(fn ($p) => ['id' => $p->id, 'nombre' => $p->nombre])->values()->all(),
                ])->values()->all(),
        ])->values()->all();
    }

    /**
     * Stock disponible de los productos que descuentan los items del catálogo.
     *
     * @param  Collection<int,MenuItem>  $items
     * @param  array<int,float>  $devueltos  lo que ya consumió una venta en edición (se suma de vuelta)
     * @return array<int,float>
     */
    public function stockPayload(Collection $items, array $devueltos = []): array
    {
        $items->loadMissing('componentes.opciones');

        $ids = $items
            ->flatMap(fn (MenuItem $i) => $i->componentes->flatMap(fn ($c) => $c->opciones->pluck('id')))
            ->merge(array_keys($devueltos))
            ->unique()
            ->all();

        $stock = $this->inventario->stockDe($ids);
        foreach ($devueltos as $productoId => $cantidad) {
            $stock[$productoId] = round(($stock[$productoId] ?? 0) + $cantidad, 2);
        }

        return $stock;
    }

    /** @return array<int,float> producto_mercado_id => cantidad que descontó la venta */
    public function consumoDeVenta(Venta $venta): array
    {
        $consumo = [];
        foreach ($venta->items as $item) {
            foreach ($item->movimientosInventario as $m) {
                $consumo[$m->producto_mercado_id] = round(($consumo[$m->producto_mercado_id] ?? 0) - (float) $m->cantidad, 2);
            }
        }

        return $consumo;
    }

    /**
     * Opciones que se eligieron en una línea ya vendida (componente_id => producto_mercado_id),
     * reconstruidas desde sus movimientos de inventario.
     *
     * @return array<int,int>
     */
    public function opcionesElegidas(VentaItem $ventaItem): array
    {
        $menuItem = $ventaItem->menuItem;
        if ($menuItem === null) {
            return [];
        }

        $productosDescontados = $ventaItem->movimientosInventario->pluck('producto_mercado_id');
        $opciones             = [];

        foreach ($menuItem->componentes as $componente) {
            if ($componente->opciones->count() < 2) {
                continue;
            }
            $elegido = $componente->opciones->pluck('id')->intersect($productosDescontados)->first();
            if ($elegido !== null) {
                $opciones[$componente->id] = (int) $elegido;
            }
        }

        return $opciones;
    }
}
