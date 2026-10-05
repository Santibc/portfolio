<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\MenuItemComponente;
use App\Models\MetodoPago;
use App\Models\MovimientoInventario;
use App\Models\ProductoMercado;
use App\Models\RegistroMercado;
use App\Models\TipoMenuItem;
use App\Models\TipoProductoMercado;
use App\Models\TurnoCaja;
use App\Models\User;
use App\Models\Venta;
use App\Services\InventarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private MetodoPago $efectivo;

    private TipoProductoMercado $tipoProducto;

    private TipoMenuItem $tipoMenu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user         = User::factory()->create();
        $this->efectivo     = MetodoPago::create(['codigo' => 'efectivo', 'nombre' => 'Efectivo', 'es_efectivo' => true, 'orden' => 1, 'activo' => true]);
        $this->tipoProducto = TipoProductoMercado::create(['nombre' => 'Snacks', 'slug' => 'snacks']);
        $this->tipoMenu     = TipoMenuItem::create(['nombre' => 'Dorilokos', 'slug' => 'dorilokos', 'orden' => 1]);
    }

    // ---------------------------------------------------------------- helpers

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function producto(string $nombre, bool $inventario = true): ProductoMercado
    {
        return ProductoMercado::create([
            'nombre'              => $nombre,
            'unidad_empaque'      => 'unidad',
            'tipo_id'             => $this->tipoProducto->id,
            'activo'              => true,
            'controla_inventario' => $inventario,
        ]);
    }

    /** @param array<string, array{0: float, 1: array<int,int>}> $componentes nombre => [cantidad, productoIds] */
    private function menuItem(string $nombre, int $precio, array $componentes = []): MenuItem
    {
        $item = MenuItem::create(['nombre' => $nombre, 'precio' => $precio, 'tipo_id' => $this->tipoMenu->id, 'activo' => true, 'orden' => 0]);
        foreach ($componentes as $nombreComp => [$cantidad, $productos]) {
            $comp = $item->componentes()->create(['nombre' => $nombreComp, 'cantidad' => $cantidad]);
            $comp->opciones()->sync($productos);
        }

        return $item;
    }

    private function componente(MenuItem $item, string $nombre): MenuItemComponente
    {
        return $item->componentes()->where('nombre', $nombre)->firstOrFail();
    }

    private function turnoAbierto(): TurnoCaja
    {
        return TurnoCaja::create(['user_apertura_id' => $this->user->id, 'abierto_en' => now(), 'base_inicial' => 0]);
    }

    private function stock(ProductoMercado $producto): float
    {
        return app(InventarioService::class)->stockDeProducto($producto->id);
    }

    private function cargarStock(ProductoMercado $producto, float $cantidad): void
    {
        app(InventarioService::class)->ajustar($producto, $cantidad, 'Stock inicial', $this->user->id);
    }

    private function vender(array $items, int $total)
    {
        return $this->actingAs($this->user)->post(route('caja.venta.store'), [
            'items' => $items,
            'pagos' => [['metodo_pago_id' => $this->efectivo->id, 'monto' => $total]],
        ]);
    }

    private function comprar(ProductoMercado $producto, float $cantidad)
    {
        return $this->actingAs($this->user)->post(route('registro-mercado.store'), [
            'producto_mercado_id' => $producto->id,
            'cantidad'            => $cantidad,
            'valor'               => 50000,
            'metodo_pago_id'      => $this->efectivo->id,
            'fecha'               => today()->toDateString(),
        ]);
    }

    // ---------------------------------------------------------------- compras de mercado

    public function test_compra_de_producto_con_inventario_suma_stock(): void
    {
        $coca = $this->producto('Coca-Cola');

        $this->comprar($coca, 50)->assertRedirect(route('registro-mercado.index'));

        $this->assertSame(50.0, $this->stock($coca));
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_mercado_id' => $coca->id,
            'tipo'                => 'compra',
            'registro_mercado_id' => RegistroMercado::first()->id,
        ]);
    }

    public function test_compra_de_producto_sin_inventario_no_genera_movimiento(): void
    {
        $carne = $this->producto('Carne molida', false);

        $this->comprar($carne, 5)->assertRedirect(route('registro-mercado.index'));

        $this->assertDatabaseCount('registros_mercado', 1);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_editar_y_eliminar_compra_recuadra_el_stock(): void
    {
        $coca = $this->producto('Coca-Cola');
        $this->comprar($coca, 50);
        $registro = RegistroMercado::first();

        $this->actingAs($this->user)->patch(route('mercado-dashboard.update', $registro), [
            'cantidad' => 30, 'valor' => 40000, 'metodo_pago_id' => $this->efectivo->id,
        ])->assertRedirect(route('mercado-dashboard.index'));
        $this->assertSame(30.0, $this->stock($coca));

        $this->actingAs($this->user)->delete(route('mercado-dashboard.destroy', $registro))
            ->assertRedirect(route('mercado-dashboard.index'));
        $this->assertSame(0.0, $this->stock($coca));
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_no_se_puede_reducir_ni_borrar_una_compra_ya_vendida(): void
    {
        $this->turnoAbierto();
        $coca = $this->producto('Coca-Cola');
        $item = $this->menuItem('Coca-Cola personal', 3000, ['Bebida' => [1, [$coca->id]]]);
        $this->comprar($coca, 10);
        $registro = RegistroMercado::first();
        $this->vender([['menu_item_id' => $item->id, 'cantidad' => 8]], 24000);

        $this->actingAs($this->user)->patch(route('mercado-dashboard.update', $registro), [
            'cantidad' => 5, 'valor' => 40000, 'metodo_pago_id' => $this->efectivo->id,
        ])->assertSessionHas('error');
        $this->assertSame(10.0, (float) $registro->fresh()->cantidad);

        $this->actingAs($this->user)->delete(route('mercado-dashboard.destroy', $registro))->assertSessionHas('error');
        $this->assertDatabaseHas('registros_mercado', ['id' => $registro->id]);
        $this->assertSame(2.0, $this->stock($coca));
    }

    // ---------------------------------------------------------------- ventas de caja

    public function test_venta_descuenta_el_sabor_de_doritos_elegido(): void
    {
        $this->turnoAbierto();
        $normales = $this->producto('Doritos normales');
        $picantes = $this->producto('Doritos picantes');
        $this->cargarStock($normales, 10);
        $this->cargarStock($picantes, 10);
        $pollo = $this->menuItem('Dorilokos de pollo', 12000, ['Sabor de Doritos' => [1, [$normales->id, $picantes->id]]]);
        $sabor = $this->componente($pollo, 'Sabor de Doritos');

        $this->vender([[
            'menu_item_id' => $pollo->id,
            'cantidad'     => 2,
            'opciones'     => [$sabor->id => $picantes->id],
        ]], 24000)->assertRedirect(route('caja.index'))->assertSessionHasNoErrors();

        $this->assertSame(8.0, $this->stock($picantes));
        $this->assertSame(10.0, $this->stock($normales));
        $this->assertDatabaseHas('venta_items', ['nombre_snapshot' => 'Dorilokos de pollo · Doritos picantes']);
    }

    public function test_item_sin_componentes_se_vende_sin_tocar_inventario(): void
    {
        $this->turnoAbierto();
        $plato = $this->menuItem('Salchipapa', 9000);

        $this->vender([['menu_item_id' => $plato->id, 'cantidad' => 1]], 9000)->assertSessionHasNoErrors();

        $this->assertDatabaseCount('ventas', 1);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_venta_sin_stock_suficiente_se_rechaza_y_no_se_guarda(): void
    {
        $this->turnoAbierto();
        $coca = $this->producto('Coca-Cola');
        $this->cargarStock($coca, 2);
        $item = $this->menuItem('Coca-Cola personal', 3000, ['Bebida' => [1, [$coca->id]]]);

        $this->vender([['menu_item_id' => $item->id, 'cantidad' => 3]], 9000)->assertSessionHasErrors('caja');

        $this->assertDatabaseCount('ventas', 0);
        $this->assertSame(2.0, $this->stock($coca));
    }

    public function test_varias_lineas_que_usan_el_mismo_producto_suman_el_consumo(): void
    {
        $this->turnoAbierto();
        $coca = $this->producto('Coca-Cola');
        $this->cargarStock($coca, 3);
        $suelta = $this->menuItem('Coca-Cola personal', 3000, ['Bebida' => [1, [$coca->id]]]);
        $combo  = $this->menuItem('Combo con Coca-Cola', 15000, ['Bebida' => [1, [$coca->id]]]);

        $this->vender([
            ['menu_item_id' => $suelta->id, 'cantidad' => 2],
            ['menu_item_id' => $combo->id, 'cantidad' => 2],
        ], 36000)->assertSessionHasErrors('caja');

        $this->assertSame(3.0, $this->stock($coca));
    }

    public function test_item_elegible_requiere_elegir_una_opcion_valida(): void
    {
        $this->turnoAbierto();
        $normales = $this->producto('Doritos normales');
        $picantes = $this->producto('Doritos picantes');
        $otro     = $this->producto('Coca-Cola');
        foreach ([$normales, $picantes, $otro] as $p) {
            $this->cargarStock($p, 5);
        }
        $pollo = $this->menuItem('Dorilokos de pollo', 12000, ['Sabor de Doritos' => [1, [$normales->id, $picantes->id]]]);
        $sabor = $this->componente($pollo, 'Sabor de Doritos');

        $this->vender([['menu_item_id' => $pollo->id, 'cantidad' => 1]], 12000)->assertSessionHasErrors('caja');
        $this->vender([[
            'menu_item_id' => $pollo->id, 'cantidad' => 1, 'opciones' => [$sabor->id => $otro->id],
        ]], 12000)->assertSessionHasErrors('caja');

        $this->assertDatabaseCount('ventas', 0);
        $this->assertDatabaseCount('movimientos_inventario', 3); // solo los ajustes iniciales
    }

    public function test_combo_descuenta_varios_productos(): void
    {
        $this->turnoAbierto();
        $normales = $this->producto('Doritos normales');
        $picantes = $this->producto('Doritos picantes');
        $coca     = $this->producto('Coca-Cola');
        foreach ([$normales, $picantes, $coca] as $p) {
            $this->cargarStock($p, 5);
        }
        $combo = $this->menuItem('Combo Dorilokos + gaseosa', 15000, [
            'Sabor de Doritos' => [1, [$normales->id, $picantes->id]],
            'Bebida'           => [1, [$coca->id]],
        ]);
        $sabor = $this->componente($combo, 'Sabor de Doritos');

        $this->vender([[
            'menu_item_id' => $combo->id, 'cantidad' => 2, 'opciones' => [$sabor->id => $normales->id],
        ]], 30000)->assertSessionHasNoErrors();

        $this->assertSame(3.0, $this->stock($normales));
        $this->assertSame(5.0, $this->stock($picantes));
        $this->assertSame(3.0, $this->stock($coca));
    }

    public function test_editar_venta_cambiando_sabor_y_cantidad_recuadra_stock(): void
    {
        $this->turnoAbierto();
        $normales = $this->producto('Doritos normales');
        $picantes = $this->producto('Doritos picantes');
        $this->cargarStock($normales, 5);
        $this->cargarStock($picantes, 5);
        $pollo = $this->menuItem('Dorilokos de pollo', 12000, ['Sabor de Doritos' => [1, [$normales->id, $picantes->id]]]);
        $sabor = $this->componente($pollo, 'Sabor de Doritos');

        $this->vender([['menu_item_id' => $pollo->id, 'cantidad' => 2, 'opciones' => [$sabor->id => $normales->id]]], 24000);
        $venta = Venta::firstOrFail();
        $this->assertSame(3.0, $this->stock($normales));

        // Puede usar todo el stock de picantes y además devolver los normales que había tomado.
        $this->actingAs($this->user)->patch(route('caja.venta.update', $venta), [
            'items' => [['menu_item_id' => $pollo->id, 'cantidad' => 5, 'opciones' => [$sabor->id => $picantes->id]]],
            'pagos' => [['metodo_pago_id' => $this->efectivo->id, 'monto' => 60000]],
        ])->assertSessionHasNoErrors();

        $this->assertSame(5.0, $this->stock($normales));
        $this->assertSame(0.0, $this->stock($picantes));
    }

    public function test_editar_venta_sin_stock_no_cambia_nada(): void
    {
        $this->turnoAbierto();
        $coca = $this->producto('Coca-Cola');
        $this->cargarStock($coca, 3);
        $item = $this->menuItem('Coca-Cola personal', 3000, ['Bebida' => [1, [$coca->id]]]);
        $this->vender([['menu_item_id' => $item->id, 'cantidad' => 2]], 6000);
        $venta = Venta::firstOrFail();

        $this->actingAs($this->user)->patch(route('caja.venta.update', $venta), [
            'items' => [['menu_item_id' => $item->id, 'cantidad' => 4]],
            'pagos' => [['metodo_pago_id' => $this->efectivo->id, 'monto' => 12000]],
        ])->assertSessionHasErrors('caja');

        $this->assertSame(1.0, $this->stock($coca));
        $this->assertSame(2, (int) $venta->fresh()->items()->sum('cantidad'));
    }

    public function test_eliminar_venta_devuelve_el_stock(): void
    {
        $this->turnoAbierto();
        $coca = $this->producto('Coca-Cola');
        $this->cargarStock($coca, 5);
        $item = $this->menuItem('Coca-Cola personal', 3000, ['Bebida' => [1, [$coca->id]]]);
        $this->vender([['menu_item_id' => $item->id, 'cantidad' => 2]], 6000);
        $venta = Venta::firstOrFail();

        $this->actingAs($this->user)->delete(route('caja.venta.destroy', $venta))->assertRedirect();

        $this->assertSoftDeleted('ventas', ['id' => $venta->id]);
        $this->assertSame(5.0, $this->stock($coca));
    }

    // ---------------------------------------------------------------- configuración

    public function test_item_de_menu_guarda_componentes_solo_con_productos_con_inventario(): void
    {
        $normales = $this->producto('Doritos normales');
        $picantes = $this->producto('Doritos picantes');
        $carne    = $this->producto('Carne', false);

        $this->actingAs($this->user)->post(route('menu-items.store'), [
            'nombre' => 'Dorilokos de carne', 'precio' => 12000, 'tipo_id' => $this->tipoMenu->id, 'activo' => 1,
            'componentes' => [['nombre' => 'Sabor de Doritos', 'cantidad' => 1, 'productos' => [$normales->id, $picantes->id]]],
        ])->assertRedirect(route('menu-items.index'));

        $item = MenuItem::where('nombre', 'Dorilokos de carne')->firstOrFail();
        $this->assertEqualsCanonicalizing(
            [$normales->id, $picantes->id],
            $item->componentes()->first()->opciones->pluck('id')->all()
        );

        $this->actingAs($this->user)->post(route('menu-items.store'), [
            'nombre' => 'Otro', 'precio' => 1000, 'tipo_id' => $this->tipoMenu->id,
            'componentes' => [['nombre' => 'Proteína', 'cantidad' => 1, 'productos' => [$carne->id]]],
        ])->assertSessionHasErrors('componentes.0.productos.0');
    }

    public function test_quitar_todos_los_componentes_al_editar_un_item(): void
    {
        $coca = $this->producto('Coca-Cola');
        $item = $this->menuItem('Coca-Cola personal', 3000, ['Bebida' => [1, [$coca->id]]]);

        $this->actingAs($this->user)->patch(route('menu-items.update', $item), [
            'nombre' => 'Coca-Cola personal', 'precio' => 3000, 'tipo_id' => $this->tipoMenu->id, 'activo' => 1,
        ])->assertRedirect(route('menu-items.index'));

        $this->assertDatabaseCount('menu_item_componentes', 0);
    }

    public function test_no_se_desactiva_inventario_ni_se_borra_un_producto_vinculado_al_menu(): void
    {
        $coca = $this->producto('Coca-Cola');
        $this->menuItem('Coca-Cola personal', 3000, ['Bebida' => [1, [$coca->id]]]);

        $this->actingAs($this->user)->patch(route('productos-mercado.update', $coca), [
            'nombre' => 'Coca-Cola', 'unidad_empaque' => 'unidad', 'tipo_id' => $this->tipoProducto->id,
            'activo' => 1, 'controla_inventario' => 0,
        ])->assertSessionHasErrors('controla_inventario');
        $this->assertTrue($coca->fresh()->controla_inventario);

        $this->actingAs($this->user)->delete(route('productos-mercado.destroy', $coca))->assertSessionHas('error');
        $this->assertNotSoftDeleted('productos_mercado', ['id' => $coca->id]);
    }

    public function test_crear_producto_con_stock_inicial_lo_deja_en_el_kardex(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('productos-mercado.create'))->assertOk()->assertSee('Stock inicial');

        $this->actingAs($admin)->post(route('productos-mercado.store'), [
            'nombre' => 'Doritos picantes', 'unidad_empaque' => 'paquete', 'tipo_id' => $this->tipoProducto->id,
            'activo' => 1, 'controla_inventario' => 1, 'stock_inicial' => 24,
        ])->assertRedirect(route('productos-mercado.index'));

        $producto = ProductoMercado::where('nombre', 'Doritos picantes')->firstOrFail();
        $this->assertSame(24.0, $this->stock($producto));
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_mercado_id' => $producto->id, 'tipo' => 'ajuste', 'motivo' => 'Stock inicial', 'user_id' => $admin->id,
        ]);
    }

    public function test_stock_inicial_se_ignora_si_no_controla_inventario_y_es_solo_para_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('productos-mercado.store'), [
            'nombre' => 'Carne', 'unidad_empaque' => 'kg', 'tipo_id' => $this->tipoProducto->id,
            'activo' => 1, 'controla_inventario' => 0, 'stock_inicial' => 10,
        ])->assertRedirect(route('productos-mercado.index'));
        $this->assertDatabaseCount('movimientos_inventario', 0);

        $this->actingAs($this->user)->post(route('productos-mercado.store'), [
            'nombre' => 'Coca-Cola', 'unidad_empaque' => 'unidad', 'tipo_id' => $this->tipoProducto->id,
            'activo' => 1, 'controla_inventario' => 1, 'stock_inicial' => 10,
        ])->assertSessionHasErrors('stock_inicial');
        $this->assertDatabaseMissing('productos_mercado', ['nombre' => 'Coca-Cola']);

        // Sin stock inicial, cualquier usuario puede crear el producto con inventario.
        $this->actingAs($this->user)->post(route('productos-mercado.store'), [
            'nombre' => 'Coca-Cola', 'unidad_empaque' => 'unidad', 'tipo_id' => $this->tipoProducto->id,
            'activo' => 1, 'controla_inventario' => 1,
        ])->assertRedirect(route('productos-mercado.index'));
        $this->assertTrue(ProductoMercado::where('nombre', 'Coca-Cola')->firstOrFail()->controla_inventario);
    }

    // ---------------------------------------------------------------- ajustes y pantallas

    public function test_solo_admin_puede_ajustar_stock_y_no_puede_quedar_negativo(): void
    {
        $coca = $this->producto('Coca-Cola');

        $this->actingAs($this->user)->post(route('inventario.ajustes.store', $coca), [
            'tipo' => 'entrada', 'cantidad' => 10, 'motivo' => 'Stock inicial',
        ])->assertForbidden();

        $admin = $this->admin();
        $this->actingAs($admin)->post(route('inventario.ajustes.store', $coca), [
            'tipo' => 'entrada', 'cantidad' => 10, 'motivo' => 'Stock inicial',
        ])->assertRedirect(route('inventario.show', $coca));
        $this->assertSame(10.0, $this->stock($coca));

        $this->actingAs($admin)->post(route('inventario.ajustes.store', $coca), [
            'tipo' => 'salida', 'cantidad' => 11, 'motivo' => 'Dañadas',
        ])->assertSessionHas('error');
        $this->assertSame(10.0, $this->stock($coca));

        $this->actingAs($admin)->post(route('inventario.ajustes.store', $coca), [
            'tipo' => 'salida', 'cantidad' => 4, 'motivo' => 'Dañadas',
        ])->assertRedirect(route('inventario.show', $coca));
        $this->assertSame(6.0, $this->stock($coca));
        $this->assertSame(2, MovimientoInventario::where('producto_mercado_id', $coca->id)->count());
    }

    public function test_pantallas_de_inventario_y_caja_cargan(): void
    {
        $this->turnoAbierto();
        $normales = $this->producto('Doritos normales');
        $picantes = $this->producto('Doritos picantes');
        $this->cargarStock($normales, 3);
        $pollo = $this->menuItem('Dorilokos de pollo', 12000, ['Sabor de Doritos' => [1, [$normales->id, $picantes->id]]]);
        $sabor = $this->componente($pollo, 'Sabor de Doritos');
        $this->vender([['menu_item_id' => $pollo->id, 'cantidad' => 1, 'opciones' => [$sabor->id => $normales->id]]], 12000);

        $this->actingAs($this->user)->get(route('inventario.index'))->assertOk()->assertSee('Doritos normales');
        $this->actingAs($this->user)->get(route('inventario.show', $normales))->assertOk()->assertSee('Dorilokos de pollo · Doritos normales');
        $this->actingAs($this->user)->get(route('caja.index'))->assertOk()->assertSee('Sabor de Doritos');
        $this->actingAs($this->user)->get(route('caja.venta.edit', Venta::firstOrFail()))->assertOk();
        $this->actingAs($this->user)->get(route('menu-items.edit', $pollo))->assertOk();
        $this->actingAs($this->user)->get(route('productos-mercado.index'))->assertOk();
        $this->actingAs($this->user)->get(route('productos-mercado.edit', $normales))->assertOk()->assertSee('Stock actual');
    }
}
