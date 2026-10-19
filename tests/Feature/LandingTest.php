<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    private function conRol(string $rol): User
    {
        Role::findOrCreate($rol, 'web');
        $user = User::factory()->create();
        $user->assignRole($rol);

        return $user;
    }

    public function test_la_landing_es_publica_y_muestra_los_datos_del_negocio(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Dorilokos Mix')
            ->assertSee('Calle 65 #11-73')
            ->assertSee('https://wa.me/573142798769', false)
            ->assertSee('https://www.instagram.com/dorilokosmix/', false)
            ->assertSee('output=embed', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_cada_producto_del_menu_pide_por_whatsapp_con_el_pedido_escrito(): void
    {
        $respuesta = $this->get('/')->assertOk();
        $pedidos = array_merge(
            array_column(config('landing.menu.dorilokos'), 'pedido'),
            array_column(config('landing.menu.combos'), 'nombre'),
        );

        foreach ($pedidos as $pedido) {
            $mensaje = sprintf('¡Hola, %s! Quiero pedir: %s.', config('landing.negocio.nombre'), $pedido);
            $respuesta->assertSee('https://wa.me/573142798769?text=' . rawurlencode($mensaje), false);
        }
    }

    public function test_los_precios_salen_de_la_config_del_local(): void
    {
        config(['landing.menu.dorilokos' => [
            ['nombre' => 'Prueba', 'pedido' => 'Dorilokos de prueba', 'etiqueta' => 'El más vendido', 'descripcion' => 'Frijol y queso.', 'personal' => 18500, 'familiar' => 40500],
        ]]);

        $this->get('/')
            ->assertOk()
            ->assertSee('$ 18.500')
            ->assertSee('$ 40.500')
            ->assertSee('El más vendido');
    }

    public function test_no_usa_los_precios_de_rappi(): void
    {
        // Rappi cobra con recargo ($ 25.900 el paisa); la landing muestra los del local.
        $this->get('/')->assertOk()->assertDontSee('$ 25.900');
    }

    public function test_el_rol_ventas_sigue_entrando_directo_a_la_caja(): void
    {
        $this->actingAs($this->conRol('ventas'))
            ->get('/')
            ->assertRedirect(route('caja.index'))
            ->assertSessionMissing('error');
    }

    public function test_el_admin_tambien_ve_la_landing(): void
    {
        $this->actingAs($this->conRol('admin'))
            ->get('/')
            ->assertOk()
            ->assertSee('Acceso del equipo');
    }
}
