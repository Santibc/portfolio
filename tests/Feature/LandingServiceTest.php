<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\LandingService;
use Tests\TestCase;

class LandingServiceTest extends TestCase
{
    private function servicio(): LandingService
    {
        return new LandingService();
    }

    public function test_whatsapp_usa_el_numero_internacional_y_codifica_el_mensaje(): void
    {
        config(['landing.negocio.whatsapp' => '573001112233']);

        $this->assertSame(
            'https://wa.me/573001112233?text=Hola%20mundo%20%C2%A1ya%21',
            $this->servicio()->urlWhatsapp('Hola mundo ¡ya!')
        );
    }

    public function test_el_pedido_lleva_el_nombre_del_producto(): void
    {
        $url = $this->servicio()->urlPedido('Doriloko Paisa');

        $this->assertStringContainsString(rawurlencode('Quiero pedir: Doriloko Paisa.'), $url);
    }

    public function test_precio_en_formato_colombiano(): void
    {
        $this->assertSame('$ 25.900', $this->servicio()->precioFormateado(25900));
        $this->assertNull($this->servicio()->precioFormateado(null));
    }

    public function test_enlaces_de_mapa_sin_api_key(): void
    {
        config(['landing.negocio.consulta_mapa' => 'Dorilokos Mix, Calle 65 #11-73']);

        $this->assertStringEndsWith('&output=embed', $this->servicio()->urlMapaEmbebido());
        $this->assertStringContainsString('/dir/?api=1&destination=' . rawurlencode('Dorilokos Mix, Calle 65 #11-73'), $this->servicio()->urlComoLlegar());
    }

    public function test_la_vista_arma_el_menu_sin_tocar_la_base_de_datos(): void
    {
        $datos = $this->servicio()->vista();

        $this->assertSame('Restaurant', $datos['datosEstructurados']['@type']);
        $this->assertCount(count(config('landing.menu.dorilokos')), $datos['menu']['dorilokos']);
        $this->assertArrayHasKey('url_pedido', $datos['menu']['dorilokos'][0]);
        $this->assertArrayHasKey('url_pedido', $datos['menu']['combos'][0]);
    }

    public function test_el_destacado_es_el_producto_con_etiqueta(): void
    {
        config(['landing.menu.dorilokos' => [
            ['nombre' => 'Pollo', 'pedido' => 'Dorilokos de pollo', 'etiqueta' => null, 'descripcion' => '', 'personal' => 15000, 'familiar' => 38000],
            ['nombre' => 'Paisa', 'pedido' => 'Dorilokos paisa', 'etiqueta' => 'El más vendido', 'descripcion' => '', 'personal' => 23000, 'familiar' => 52000],
        ]]);

        $destacado = $this->servicio()->vista()['menu']['destacado'];

        $this->assertSame('Paisa', $destacado['nombre']);
        $this->assertSame('$ 23.000', $destacado['personal_formateado']);
    }
}
