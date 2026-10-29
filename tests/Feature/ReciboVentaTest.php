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
            ->assertSee('width: 58mm', false)
            ->assertDontSee('<div class="regla"', false);
    }

    public function test_recibo_no_fija_el_tamano_de_la_hoja(): void
    {
        $html = $this->actingAs($this->user)->get(route('caja.recibo.prueba'))->assertOk()->getContent();

        // En una impresora real, un tamaño de página distinto al papel del driver hace que Chrome
        // centre el recibo en la hoja e imprima la fecha y el enlace en los márgenes que sobran.
        $this->assertStringContainsString('@page { margin: 0; }', $html);
        $this->assertDoesNotMatchRegularExpression('/@page\s*\{[^}]*\bsize\s*:/', $html);
        // Pegado a la izquierda y angosto: en la XP-58 del POS solo salen ~42 mm desde el borde izquierdo.
        $this->assertStringContainsString('.recibo { width: 42mm; max-width: 100%; margin: 0;', $html);
    }

    public function test_recibo_termina_con_la_web_del_negocio_y_el_programador(): void
    {
        $this->actingAs($this->user)->get(route('caja.recibo.prueba'))
            ->assertOk()
            ->assertSeeInOrder(['¡Gracias por su compra!', parse_url(route('welcome'), PHP_URL_HOST), 'Desarrollador: Santiago Bellaizan · Tel: 320 223 0467']);
    }

    public function test_recibo_de_prueba_no_registra_ventas(): void
    {
        $this->actingAs($this->user)->get(route('caja.recibo.prueba'))
            ->assertOk()
            ->assertSee('Recibo de prueba')
            ->assertSee('Prueba de impresión')
            ->assertSee('<div class="regla"', false)
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
        // Pantalla completa como único programa + impresión sin la ventana de Chrome.
        $this->assertStringContainsString('--kiosk --kiosk-printing', $script);
        // El POS se usa con teclado: el de la pantalla sale solo si no hay uno conectado.
        $this->assertStringNotContainsString('--disable-usb-keyboard-detect', $script);
        $this->assertStringContainsString("'TouchKeyboardTapInvoke' -Value 1", $script);
        $this->assertStringContainsString("'EnableDesktopModeAutoInvoke' -Value 1", $script);
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
