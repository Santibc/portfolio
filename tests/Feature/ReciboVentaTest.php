<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LandingAjuste;
use App\Models\MenuItem;
use App\Models\MetodoPago;
use App\Models\ProductoMercado;
use App\Models\TipoMenuItem;
use App\Models\TipoProductoMercado;
use App\Models\TurnoCaja;
use App\Models\User;
use App\Models\Venta;
use App\Services\InventarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReciboVentaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private MetodoPago $efectivo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user     = User::factory()->create(['name' => 'Ana Cajera']);
        $this->efectivo = MetodoPago::create(['codigo' => 'efectivo', 'nombre' => 'Efectivo', 'es_efectivo' => true, 'orden' => 1, 'activo' => true]);
    }

    // ---------------------------------------------------------------- helpers

    private function vendedor(): User
    {
        Role::findOrCreate('ventas', 'web');
        $vendedor = User::factory()->create();
        $vendedor->assignRole('ventas');

        return $vendedor;
    }

    /** Vende 2 "Onces con gaseosa" eligiendo la gaseosa de manzana, pagando $30.000 en efectivo. */
    private function venderOnces(): Venta
    {
        TurnoCaja::create(['user_apertura_id' => $this->user->id, 'abierto_en' => now(), 'base_inicial' => 0]);

        $tipoProducto = TipoProductoMercado::create(['nombre' => 'Gaseosas', 'slug' => 'gaseosas']);
        [$cola, $manzana] = collect(['Coca-Cola 400 ml', 'Gaseosa manzana 400 ml'])->map(function (string $nombre) use ($tipoProducto) {
            $producto = ProductoMercado::create(['nombre' => $nombre, 'unidad_empaque' => 'unidad', 'tipo_id' => $tipoProducto->id, 'activo' => true, 'controla_inventario' => true]);
            app(InventarioService::class)->ajustar($producto, 10, 'Stock inicial', $this->user->id);

            return $producto;
        })->all();

        $tipoMenu = TipoMenuItem::create(['nombre' => 'Combos', 'slug' => 'combos', 'orden' => 1]);
        $onces    = MenuItem::create(['nombre' => 'Onces con gaseosa', 'precio' => 12000, 'tipo_id' => $tipoMenu->id, 'activo' => true, 'orden' => 0]);
        $gaseosa  = $onces->componentes()->create(['nombre' => 'Gaseosa', 'cantidad' => 1]);
        $gaseosa->opciones()->sync([$cola->id, $manzana->id]);

        $this->actingAs($this->user)->post(route('caja.venta.store'), [
            'items' => [['menu_item_id' => $onces->id, 'cantidad' => 2, 'opciones' => [$gaseosa->id => $manzana->id]]],
            'pagos' => [['metodo_pago_id' => $this->efectivo->id, 'monto' => 30000]],
        ])->assertRedirect(route('caja.index'))->assertSessionHasNoErrors();

        return Venta::firstOrFail();
    }

    // ---------------------------------------------------------------- recibo

    public function test_al_cobrar_queda_pendiente_el_recibo_de_la_venta(): void
    {
        $venta = $this->venderOnces();

        // La caja lo imprime al recargar; el mensaje de éxito sigue igual.
        $this->assertSame($venta->id, session('recibo_venta_id'));
        $this->assertStringContainsString('Venta registrada', (string) session('success'));
    }

    public function test_recibo_muestra_negocio_items_opcion_elegida_pagos_y_cambio(): void
    {
        $venta = $this->venderOnces();

        $this->actingAs($this->user)->get(route('caja.venta.recibo', $venta))
            ->assertOk()
            ->assertSee('El Pan Dorado DC')
            ->assertSee('Carrera 41A # 4-42, Primavera, Bogotá D.C.')
            ->assertSee('Tel. 318 865 5792')
            ->assertSee('Recibo N.º '.$venta->id)
            ->assertSee('Ana Cajera')
            ->assertSeeInOrder(['2 x Onces con gaseosa', '$ 24.000', 'Gaseosa manzana 400 ml'])
            ->assertSeeInOrder(['TOTAL', '$ 24.000', 'Efectivo', '$ 30.000', 'Cambio', '$ 6.000'])
            ->assertSee('size: 58mm', false);
    }

    public function test_recibo_se_ajusta_al_rollo_de_80_mm(): void
    {
        $venta = $this->venderOnces();

        $this->actingAs($this->user)->get(route('caja.venta.recibo', ['venta' => $venta, 'papel' => 80]))
            ->assertOk()
            ->assertSee('size: 80mm', false)
            ->assertSee('width: 70mm', false);
    }

    public function test_recibo_de_prueba_no_registra_ventas(): void
    {
        $this->actingAs($this->user)->get(route('caja.recibo.prueba'))
            ->assertOk()
            ->assertSee('Recibo de prueba')
            ->assertSee('Prueba de impresión')
            ->assertSeeInOrder(['TOTAL', '$ 117.500', 'Efectivo', '$ 120.000', 'Cambio', '$ 2.500']);

        $this->assertDatabaseCount('ventas', 0);
    }

    // ---------------------------------------------------------------- instalador del equipo POS

    public function test_instalador_abre_la_caja_con_impresion_directa(): void
    {
        $respuesta = $this->actingAs($this->user)->get(route('caja.instalador'))->assertOk();
        $script    = $respuesta->getContent();

        $this->assertStringContainsString('attachment; filename="instalar-caja.bat"', (string) $respuesta->headers->get('Content-Disposition'));
        $this->assertStringStartsWith("<# :\r\n@echo off\r\n", $script);
        $this->assertStringNotContainsString("\n", str_replace("\r\n", '', $script), 'cmd.exe necesita CRLF en todas las líneas');
        $this->assertStringContainsString("\$UrlCaja       = '".route('login')."'", $script);
        $this->assertStringContainsString("\$NombreAcceso  = 'Caja El Pan Dorado DC'", $script);
        $this->assertStringContainsString('--kiosk-printing', $script);
        $this->assertStringNotContainsString('__URL_CAJA__', $script);
    }

    public function test_instalador_escapa_comillas_del_nombre_del_negocio(): void
    {
        LandingAjuste::actual()->update(['nombre' => "Pan D'Oro: centro"]);

        $script = $this->actingAs($this->user)->get(route('caja.instalador'))->getContent();

        // Comilla doblada (literal de PowerShell) y sin ":" (no va en un nombre de archivo .lnk).
        $this->assertStringContainsString("\$NombreAcceso  = 'Caja Pan D''Oro centro'", $script);
    }

    // ---------------------------------------------------------------- acceso

    public function test_vendedor_imprime_recibos_y_baja_el_instalador(): void
    {
        $venta    = $this->venderOnces();
        $vendedor = $this->vendedor();

        $this->actingAs($vendedor)->get(route('caja.venta.recibo', $venta))->assertOk()->assertSee('Recibo N.º '.$venta->id);
        $this->actingAs($vendedor)->get(route('caja.recibo.prueba'))->assertOk();
        $this->actingAs($vendedor)->get(route('caja.instalador'))->assertOk();
    }

    public function test_invitado_no_ve_recibos(): void
    {
        $venta = $this->venderOnces();
        auth()->logout();

        $this->get(route('caja.venta.recibo', $venta))->assertRedirect(route('login'));
        $this->get(route('caja.instalador'))->assertRedirect(route('login'));
    }
}
