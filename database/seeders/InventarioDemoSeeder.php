<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\MenuItemComponente;
use App\Models\ProductoMercado;
use App\Models\TipoMenuItem;
use App\Models\TipoProductoMercado;
use App\Models\User;
use App\Services\InventarioService;
use Illuminate\Database\Seeder;

/**
 * Demo de inventario Dorilokos: sabores de Doritos y gaseosas con stock, items del menú que los
 * descuentan (el cajero elige el sabor) y un ajuste de stock inicial. Solo para BD de pruebas.
 */
class InventarioDemoSeeder extends Seeder
{
    public function run(InventarioService $inventario): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();

        $snacks = TipoProductoMercado::firstOrCreate(['slug' => 'snacks'], ['nombre' => 'Snacks']);
        $doritos = collect(['Doritos normales', 'Doritos picantes', 'Doritos flamin hot'])
            ->map(fn (string $nombre) => ProductoMercado::updateOrCreate(
                ['nombre' => $nombre],
                ['unidad_empaque' => 'paquete', 'tipo_id' => $snacks->id, 'activo' => true, 'controla_inventario' => true]
            ));

        $gaseosas = ProductoMercado::whereIn('nombre', ['Coca-Cola 1.5L', 'Pepsi 1.5L', 'Postobón Manzana'])->get();
        $gaseosas->each->update(['controla_inventario' => true]);

        $tipoDorilokos = TipoMenuItem::updateOrCreate(['slug' => 'dorilokos'], ['nombre' => 'Dorilokos', 'orden' => 0]);
        $dorilokos = [
            'Dorilokos paisa'   => 14000,
            'Dorilokos de carne' => 12000,
            'Dorilokos de pollo' => 12000,
            'Dorilokos mixto'   => 15000,
        ];
        foreach ($dorilokos as $nombre => $precio) {
            $item = MenuItem::updateOrCreate(
                ['nombre' => $nombre],
                ['precio' => $precio, 'tipo_id' => $tipoDorilokos->id, 'activo' => true, 'orden' => 0]
            );
            $this->componente($item, 'Sabor de Doritos', $doritos->pluck('id')->all());
        }

        $gaseosa = MenuItem::where('nombre', 'Gaseosa')->first();
        if ($gaseosa && $gaseosas->isNotEmpty()) {
            $this->componente($gaseosa, 'Bebida', $gaseosas->pluck('id')->all());
        }

        if ($admin) {
            foreach ($doritos->concat($gaseosas) as $producto) {
                if ($inventario->stockDeProducto($producto->id) <= 0) {
                    $inventario->ajustar($producto, 24, 'Stock inicial (demo)', $admin->id);
                }
            }
        }
    }

    /** @param array<int,int> $productoIds */
    private function componente(MenuItem $item, string $nombre, array $productoIds): void
    {
        MenuItemComponente::where('menu_item_id', $item->id)->delete();
        $componente = $item->componentes()->create(['nombre' => $nombre, 'cantidad' => 1, 'orden' => 0]);
        $componente->opciones()->sync($productoIds);
    }
}
