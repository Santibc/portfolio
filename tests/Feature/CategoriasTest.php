<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\ProductoMercado;
use App\Models\TipoMenuItem;
use App\Models\TipoProductoMercado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoriasTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    // ---------------------------------------------------------------- helpers

    private function tipoMenu(string $nombre = 'Combos'): TipoMenuItem
    {
        return TipoMenuItem::create(['nombre' => $nombre, 'slug' => str($nombre)->slug()->toString(), 'orden' => 1]);
    }

    private function itemMenu(TipoMenuItem $tipo): MenuItem
    {
        return MenuItem::create(['nombre' => 'Desayuno', 'precio' => 12000, 'tipo_id' => $tipo->id, 'activo' => true, 'orden' => 0]);
    }

    private function tipoProducto(string $nombre = 'Gaseosas'): TipoProductoMercado
    {
        return TipoProductoMercado::create(['nombre' => $nombre, 'slug' => str($nombre)->slug()->toString()]);
    }

    private function producto(TipoProductoMercado $tipo): ProductoMercado
    {
        return ProductoMercado::create(['nombre' => 'Coca-Cola 400 ml', 'unidad_empaque' => 'unidad', 'tipo_id' => $tipo->id, 'activo' => true, 'controla_inventario' => false]);
    }

    // ---------------------------------------------------------------- categorías del menú

    public function test_elimina_la_categoria_del_menu_cuando_sus_items_ya_se_eliminaron(): void
    {
        $tipo = $this->tipoMenu();
        $item = $this->itemMenu($tipo);
        $item->delete();

        // Antes: el item eliminado (soft delete) seguía apuntando a la categoría y la FK daba error 500.
        $this->actingAs($this->admin)->delete(route('menu-items.tipos.destroy', $tipo))
            ->assertRedirect(route('menu-items.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted($tipo);
        $this->assertSame('Combos', MenuItem::withTrashed()->find($item->id)->tipo->nombre);
    }

    public function test_no_elimina_la_categoria_del_menu_con_items_activos(): void
    {
        $tipo = $this->tipoMenu();
        $this->itemMenu($tipo);

        $this->actingAs($this->admin)->delete(route('menu-items.tipos.destroy', $tipo))
            ->assertRedirect(route('menu-items.index'))
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted($tipo);
    }

    public function test_crear_una_categoria_del_menu_con_el_nombre_de_una_eliminada_la_recupera(): void
    {
        $tipo = $this->tipoMenu();
        $tipo->delete();

        $this->actingAs($this->admin)->post(route('menu-items.tipos.store'), ['nombre' => 'Combos', 'orden' => 4])
            ->assertRedirect(route('menu-items.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tipos_menu_item', 1);
        $this->assertNotSoftDeleted($tipo);
        $this->assertSame(4, $tipo->fresh()->orden);
    }

    public function test_un_item_del_menu_no_puede_usar_una_categoria_eliminada(): void
    {
        $tipo = $this->tipoMenu();
        $tipo->delete();

        $this->actingAs($this->admin)->post(route('menu-items.store'), ['nombre' => 'Desayuno', 'precio' => 12000, 'tipo_id' => $tipo->id])
            ->assertSessionHasErrors('tipo_id');
    }

    // ---------------------------------------------------------------- categorías de productos

    public function test_elimina_la_categoria_de_productos_cuando_sus_productos_ya_se_eliminaron(): void
    {
        $tipo     = $this->tipoProducto();
        $producto = $this->producto($tipo);
        $producto->delete();

        $this->actingAs($this->admin)->delete(route('productos-mercado.tipos.destroy', $tipo))
            ->assertRedirect(route('productos-mercado.tipos.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted($tipo);
        $this->assertSame('Gaseosas', ProductoMercado::withTrashed()->find($producto->id)->tipo->nombre);
    }

    public function test_no_elimina_la_categoria_de_productos_con_productos_activos(): void
    {
        $tipo = $this->tipoProducto();
        $this->producto($tipo);

        $this->actingAs($this->admin)->delete(route('productos-mercado.tipos.destroy', $tipo))
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted($tipo);
    }

    public function test_crear_una_categoria_de_productos_con_el_nombre_de_una_eliminada_la_recupera(): void
    {
        $tipo = $this->tipoProducto();
        $tipo->delete();

        $this->actingAs($this->admin)->post(route('productos-mercado.tipos.store'), ['nombre' => 'Gaseosas'])
            ->assertRedirect(route('productos-mercado.tipos.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tipos_producto_mercado', 1);
        $this->assertNotSoftDeleted($tipo);
        $this->actingAs($this->admin)->get(route('productos-mercado.tipos.index'))->assertOk()->assertSee('Gaseosas');
    }

    public function test_la_lista_de_categorias_de_productos_no_muestra_las_eliminadas(): void
    {
        $this->tipoProducto('Plaza')->delete();
        $this->tipoProducto('Desechables');

        $this->actingAs($this->admin)->get(route('productos-mercado.tipos.index'))
            ->assertOk()
            ->assertSee('Desechables')
            ->assertDontSee('Plaza');
    }
}
