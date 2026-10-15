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

    public function test_la_landing_muestra_los_datos_de_la_panaderia(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('El Pan Dorado DC')
            ->assertSee('Carrera 41A # 4-42')
            ->assertSee('318 865 5792')
            ->assertSee('Dorahaliz Corzo Quijano')
            ->assertSee('https://wa.me/573188655792?text=', false)
            ->assertSee('tel:+573188655792', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_sin_api_key_el_mapa_usa_el_embed_publico(): void
    {
        config(['panaderia.maps_api_key' => null]);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://maps.google.com/maps?q=Carrera%2041A%20%23%204-42', false)
            ->assertSee('output=embed', false)
            ->assertDontSee('maps/embed/v1/place', false);
    }

    public function test_con_api_key_el_mapa_usa_la_maps_embed_api(): void
    {
        config(['panaderia.maps_api_key' => 'clave-de-prueba']);

        $this->get('/')
            ->assertOk()
            ->assertSee('https://www.google.com/maps/embed/v1/place?key=clave-de-prueba', false);
    }

    public function test_el_vendedor_sigue_yendo_a_la_caja_desde_la_raiz(): void
    {
        Role::findOrCreate('ventas', 'web');
        $vendedor = User::factory()->create();
        $vendedor->assignRole('ventas');

        $this->actingAs($vendedor)->get('/')->assertRedirect(route('caja.index'));
    }
}
