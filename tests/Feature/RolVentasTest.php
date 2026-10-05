<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\MetodoPago;
use App\Models\TipoMenuItem;
use App\Models\TurnoCaja;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolVentasTest extends TestCase
{
    use RefreshDatabase;

    private function conRol(string $rol): User
    {
        Role::findOrCreate($rol, 'web');
        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    private function efectivo(): MetodoPago
    {
        return MetodoPago::create(['codigo' => 'efectivo', 'nombre' => 'Efectivo', 'es_efectivo' => true, 'orden' => 1, 'activo' => true]);
    }

    private function item(): MenuItem
    {
        $tipo = TipoMenuItem::create(['nombre' => 'Dorilokos', 'slug' => 'dorilokos', 'orden' => 1]);

        return MenuItem::create(['nombre' => 'Dorilokos de pollo', 'precio' => 12000, 'tipo_id' => $tipo->id, 'activo' => true, 'orden' => 0]);
    }

    // ---------------------------------------------------------------- rol ventas

    public function test_vendedor_abre_caja_vende_y_cierra(): void
    {
        $vendedor = $this->conRol('ventas');
        $efectivo = $this->efectivo();
        $item     = $this->item();

        $this->actingAs($vendedor)->get(route('caja.index'))->assertOk();

        $this->actingAs($vendedor)->post(route('caja.turno.abrir'), ['base_inicial' => 50000])
            ->assertRedirect(route('caja.index'));
        $turno = TurnoCaja::firstOrFail();

        $this->actingAs($vendedor)->post(route('caja.venta.store'), [
            'items' => [['menu_item_id' => $item->id, 'cantidad' => 1]],
            'pagos' => [['metodo_pago_id' => $efectivo->id, 'monto' => 12000]],
        ])->assertRedirect(route('caja.index'))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('ventas', 1);

        // Al cerrar vuelve a la caja (no al dashboard de caja, que no puede ver).
        $this->actingAs($vendedor)->post(route('caja.turno.cerrar', $turno), ['total_declarado' => 62000])
            ->assertRedirect(route('caja.index'));
        $this->assertNotNull($turno->fresh()->cerrado_en);
    }

    public function test_vendedor_no_entra_a_otros_modulos(): void
    {
        $vendedor = $this->conRol('ventas');

        // Inicio (después del login) lleva a la caja sin mensaje de error.
        $this->actingAs($vendedor)->get(route('dashboard'))
            ->assertRedirect(route('caja.index'))
            ->assertSessionMissing('error');

        foreach (['caja-dashboard.index', 'menu-items.index', 'productos-mercado.index', 'inventario.index', 'gastos.index', 'nomina.index', 'usuarios.index', 'components.showcase'] as $ruta) {
            $this->actingAs($vendedor)->get(route($ruta))
                ->assertRedirect(route('caja.index'))
                ->assertSessionHas('error');
        }
    }

    public function test_vendedor_no_puede_editar_ni_borrar_ventas_ni_escribir_en_otros_modulos(): void
    {
        $vendedor = $this->conRol('ventas');
        $efectivo = $this->efectivo();
        $item     = $this->item();
        $turno    = TurnoCaja::create(['user_apertura_id' => $vendedor->id, 'abierto_en' => now(), 'base_inicial' => 0]);
        $venta    = Venta::create(['turno_caja_id' => $turno->id, 'user_id' => $vendedor->id, 'total' => 12000]);

        $this->actingAs($vendedor)->patch(route('caja.venta.update', $venta), [
            'items' => [['menu_item_id' => $item->id, 'cantidad' => 2]],
            'pagos' => [['metodo_pago_id' => $efectivo->id, 'monto' => 24000]],
        ])->assertForbidden();
        $this->actingAs($vendedor)->delete(route('caja.venta.destroy', $venta))->assertForbidden();
        $this->actingAs($vendedor)->post(route('menu-items.store'), ['nombre' => 'X', 'precio' => 1, 'tipo_id' => $item->tipo_id])
            ->assertForbidden();

        $this->assertNotSoftDeleted('ventas', ['id' => $venta->id]);
        $this->assertDatabaseCount('menu_items', 1);
    }

    public function test_vendedor_ve_solo_la_caja_en_el_menu_y_puede_usar_su_perfil(): void
    {
        $vendedor = $this->conRol('ventas');

        $this->actingAs($vendedor)->get(route('caja.index'))
            ->assertOk()
            ->assertSee('Registrar ventas')
            ->assertDontSee(route('productos-mercado.index'))
            ->assertDontSee(route('caja-dashboard.index'));

        $this->actingAs($vendedor)->get(route('profile.edit'))->assertOk();
        $this->actingAs($vendedor)->patch(route('profile.theme'), ['theme' => 'dark'])->assertOk();
    }

    public function test_admin_y_usuarios_sin_rol_no_se_ven_afectados(): void
    {
        $admin = $this->conRol('admin');
        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('productos-mercado.index'))->assertOk();

        $sinRol = User::factory()->create();
        $this->actingAs($sinRol)->get(route('productos-mercado.index'))->assertOk();
    }

    // ---------------------------------------------------------------- módulo usuarios

    public function test_admin_crea_un_usuario_de_ventas_que_puede_iniciar_sesion(): void
    {
        $admin = $this->conRol('admin');

        $this->actingAs($admin)->get(route('usuarios.create'))->assertOk();
        $this->actingAs($admin)->post(route('usuarios.store'), [
            'name' => 'Laura Caja', 'email' => 'laura@caja.com', 'rol' => 'ventas',
            'password' => 'secreto123', 'password_confirmation' => 'secreto123',
        ])->assertRedirect(route('usuarios.index'));

        $laura = User::where('email', 'laura@caja.com')->firstOrFail();
        $this->assertTrue($laura->esVendedor());
        $this->assertTrue(Hash::check('secreto123', $laura->password));

        $this->actingAs($admin)->get(route('usuarios.index'))->assertOk()->assertSee('Laura Caja');

        auth()->logout();
        $this->post(route('login'), ['email' => 'laura@caja.com', 'password' => 'secreto123'])->assertRedirect();
        $this->assertAuthenticatedAs($laura);
    }

    public function test_editar_usuario_cambia_rol_y_la_contrasena_es_opcional(): void
    {
        $admin = $this->conRol('admin');
        $user  = $this->conRol('ventas');
        $hashAnterior = $user->password;

        $this->actingAs($admin)->get(route('usuarios.edit', $user))->assertOk();
        $this->actingAs($admin)->patch(route('usuarios.update', $user), [
            'name' => 'Nuevo nombre', 'email' => $user->email, 'rol' => 'admin',
        ])->assertRedirect(route('usuarios.index'));

        $user->refresh();
        $this->assertSame('Nuevo nombre', $user->name);
        $this->assertSame($hashAnterior, $user->password);
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->hasRole('ventas'));
    }

    public function test_no_se_queda_el_sistema_sin_administrador(): void
    {
        $admin = $this->conRol('admin');

        $this->actingAs($admin)->patch(route('usuarios.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'rol' => 'ventas',
        ])->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->isAdmin());

        $this->actingAs($admin)->delete(route('usuarios.destroy', $admin))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_eliminar_usuario_sin_movimientos_y_bloquear_si_tiene_ventas(): void
    {
        $admin  = $this->conRol('admin');
        $libre  = $this->conRol('ventas');
        $conTurno = $this->conRol('ventas');
        TurnoCaja::create(['user_apertura_id' => $conTurno->id, 'abierto_en' => now(), 'base_inicial' => 0]);

        $this->actingAs($admin)->delete(route('usuarios.destroy', $libre))->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseMissing('users', ['id' => $libre->id]);

        $this->actingAs($admin)->delete(route('usuarios.destroy', $conTurno))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $conTurno->id]);
    }

    public function test_solo_admin_gestiona_usuarios(): void
    {
        $sinRol = User::factory()->create();

        $this->actingAs($sinRol)->get(route('usuarios.index'))->assertForbidden();
        $this->actingAs($sinRol)->post(route('usuarios.store'), [
            'name' => 'X', 'email' => 'x@x.com', 'rol' => 'admin', 'password' => 'secreto123', 'password_confirmation' => 'secreto123',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'x@x.com']);
    }
}
