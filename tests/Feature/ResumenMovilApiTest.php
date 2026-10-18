<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\MenuItem;
use App\Models\MetodoPago;
use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\PagoNomina;
use App\Models\ProductoMercado;
use App\Models\TipoMenuItem;
use App\Models\TipoProductoMercado;
use App\Models\TurnoCaja;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumenMovilApiTest extends TestCase
{
    use RefreshDatabase;

    private const LLAVE = 'llave-de-prueba-app-movil';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.app_movil.key' => self::LLAVE]);
    }

    private function getResumen(array $query = [], ?string $llave = self::LLAVE)
    {
        $headers = $llave === null ? [] : ['X-Api-Key' => $llave];

        return $this->getJson('/api/movil/resumen?'.http_build_query($query), $headers);
    }

    /** Turno abierto con 2 ventas (efectivo 10.000 con cambio, nequi 5.000), un producto agotado y nómina aprobada con saldo. */
    private function escenario(): void
    {
        $admin    = User::factory()->create();
        $efectivo = MetodoPago::create(['codigo' => 'efectivo', 'nombre' => 'Efectivo', 'es_efectivo' => true, 'orden' => 1, 'activo' => true]);
        $nequi    = MetodoPago::create(['codigo' => 'nequi', 'nombre' => 'Nequi', 'es_efectivo' => false, 'orden' => 2, 'activo' => true]);

        $turno = TurnoCaja::create(['user_apertura_id' => $admin->id, 'abierto_en' => now(), 'base_inicial' => 50_000]);

        $tipoMenu = TipoMenuItem::create(['nombre' => 'Menú', 'slug' => 'menu', 'orden' => 1]);
        $papa     = MenuItem::create(['nombre' => 'Papa rellena', 'precio' => 5_000, 'tipo_id' => $tipoMenu->id, 'activo' => true, 'orden' => 0]);
        $gaseosa  = MenuItem::create(['nombre' => 'Gaseosa', 'precio' => 2_500, 'tipo_id' => $tipoMenu->id, 'activo' => true, 'orden' => 1]);

        $v1 = Venta::create(['turno_caja_id' => $turno->id, 'user_id' => $admin->id, 'total' => 10_000, 'efectivo_recibido' => 12_000, 'cambio' => 2_000]);
        $v1->pagos()->create(['metodo_pago_id' => $efectivo->id, 'monto' => 12_000]);
        $v1->items()->create(['menu_item_id' => $papa->id, 'nombre_snapshot' => 'Papa rellena', 'precio_unitario' => 5_000, 'cantidad' => 2, 'subtotal' => 10_000]);

        $v2 = Venta::create(['turno_caja_id' => $turno->id, 'user_id' => $admin->id, 'total' => 5_000, 'efectivo_recibido' => 0, 'cambio' => 0]);
        $v2->pagos()->create(['metodo_pago_id' => $nequi->id, 'monto' => 5_000]);
        $v2->items()->create(['menu_item_id' => $gaseosa->id, 'nombre_snapshot' => 'Gaseosa', 'precio_unitario' => 2_500, 'cantidad' => 2, 'subtotal' => 5_000]);

        $tipo = TipoProductoMercado::create(['nombre' => 'Bebidas', 'slug' => 'bebidas']);
        ProductoMercado::create(['nombre' => 'Coca-Cola', 'unidad_empaque' => 'und', 'tipo_id' => $tipo->id, 'controla_inventario' => true]);

        $empleado = Empleado::create([
            'metodo_pago_id' => $nequi->id, 'nombre' => 'Empleado Test', 'documento' => '900001',
            'salario_base' => 1_000_000, 'auxilio_transporte' => 0, 'tiene_auxilio' => false, 'bono_default' => 0,
            'porcentaje_salud' => 4, 'porcentaje_pension' => 4, 'fecha_ingreso' => '2025-01-01', 'activo' => true,
        ]);
        $nomina = Nomina::create([
            'creada_por' => $admin->id, 'tipo' => 'quincenal',
            'fecha_inicio' => now()->startOfMonth()->toDateString(), 'fecha_fin' => now()->endOfMonth()->toDateString(),
            'descripcion' => 'Periodo test', 'dias' => 15, 'estado' => 'aprobada',
        ]);
        $detalle = NominaDetalle::create([
            'nomina_id' => $nomina->id, 'empleado_id' => $empleado->id, 'empleado_nombre' => 'Empleado Test',
            'dias' => 15, 'salario_base' => 1_000_000, 'auxilio' => 0, 'bono' => 0,
            'porcentaje_salud' => 4, 'porcentaje_pension' => 4, 'basico' => 1_000_000,
            'salud' => 40_000, 'pension' => 40_000, 'total_devengado' => 1_000_000,
            'total_deducido' => 80_000, 'neto' => 920_000, 'ahorro' => 0,
        ]);
        PagoNomina::create([
            'nomina_detalle_id' => $detalle->id, 'metodo_pago_id' => $nequi->id, 'user_id' => $admin->id,
            'monto' => 20_000, 'fecha_pago' => now()->toDateString(),
        ]);
    }

    public function test_sin_llave_responde_401(): void
    {
        $this->getResumen([], null)->assertStatus(401);
    }

    public function test_llave_incorrecta_responde_401(): void
    {
        $this->getResumen([], 'otra-llave')->assertStatus(401);
    }

    public function test_sin_llave_configurada_la_api_queda_cerrada(): void
    {
        config(['services.app_movil.key' => null]);

        $this->getResumen([], '')->assertStatus(401);
    }

    public function test_devuelve_el_resumen_del_periodo(): void
    {
        $this->escenario();
        $hoy = now()->toDateString();

        $json = $this->getResumen(['desde' => $hoy, 'hasta' => $hoy])
            ->assertOk()
            ->assertJsonStructure([
                'negocio', 'periodo' => ['desde', 'hasta'],
                'totales' => ['ingresos', 'egresos', 'neto', 'egresosPorModulo' => ['caja', 'mercado', 'nomina', 'fijos'], 'porMetodo'],
                'ventas' => ['cantidad', 'total', 'ticketPromedio', 'porDia', 'topItems'],
                'cajaAbierta' => ['abiertoEn', 'base', 'cantidadVentas', 'ventas', 'gastos', 'efectivoEsperado'],
                'alertas' => ['productosAgotados', 'nominaPendiente'],
                'generadoEn',
            ])
            ->json();

        // Ingresos del consolidado: efectivo neto del cambio (10.000) + nequi (5.000).
        $this->assertSame(15_000, $json['totales']['ingresos']);
        $this->assertSame(20_000, $json['totales']['egresos']); // pago de nómina
        $this->assertSame(-5_000, $json['totales']['neto']);

        $this->assertSame(2, $json['ventas']['cantidad']);
        $this->assertSame(7_500, $json['ventas']['ticketPromedio']);
        $this->assertSame([['fecha' => $hoy, 'total' => 15_000, 'cantidad' => 2]], $json['ventas']['porDia']);
        $this->assertSame('Papa rellena', $json['ventas']['topItems'][0]['nombre']);

        $this->assertSame(2, $json['cajaAbierta']['cantidadVentas']);
        $this->assertSame(60_000, $json['cajaAbierta']['efectivoEsperado']); // base 50.000 + efectivo neto 10.000

        $this->assertSame([['nombre' => 'Coca-Cola', 'unidad' => 'und']], $json['alertas']['productosAgotados']);
        $this->assertSame(900_000, $json['alertas']['nominaPendiente']);
    }

    public function test_rellena_con_cero_los_dias_sin_ventas(): void
    {
        $json = $this->getResumen(['desde' => '2026-01-01', 'hasta' => '2026-01-03'])->assertOk()->json();

        $this->assertCount(3, $json['ventas']['porDia']);
        $this->assertSame(0, $json['ventas']['porDia'][1]['total']);
        $this->assertNull($json['cajaAbierta']);
    }

    public function test_rango_invalido_responde_422(): void
    {
        $this->getResumen(['desde' => '2026-02-10', 'hasta' => '2026-02-01'])->assertStatus(422);
        $this->getResumen(['desde' => '2024-01-01', 'hasta' => '2026-01-01'])->assertStatus(422);
        $this->getResumen(['desde' => 'ayer'])->assertStatus(422);
    }
}
