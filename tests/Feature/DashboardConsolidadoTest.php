<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ConceptoGastoFijo;
use App\Models\Empleado;
use App\Models\Gasto;
use App\Models\GastoFijo;
use App\Models\MetodoPago;
use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\PagoNomina;
use App\Models\ProductoMercado;
use App\Models\RegistroMercado;
use App\Models\TipoProductoMercado;
use App\Models\TurnoCaja;
use App\Models\User;
use App\Models\Venta;
use App\Services\ConsolidadoContableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardConsolidadoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    /**
     * Construye un escenario con las 4 fuentes de egreso + ingresos de caja,
     * todo dentro del mes en curso. Devuelve [efectivo, nequi] (MetodoPago).
     *
     * Esperado por método:
     *   Efectivo → ingresos 10.000 ; egresos caja(3.000+1.000 ahorro)+fijos 1.500 = 5.500 ; neto 4.500
     *   Nequi    → ingresos  5.000 ; egresos mercado 2.000 + nómina 2.500 = 4.500       ; neto   500
     *   Totales  → ingresos 15.000 ; egresos 10.000 ; neto 5.000
     *
     * @return array{0: MetodoPago, 1: MetodoPago}
     */
    private function escenario(User $admin): array
    {
        $efectivo = MetodoPago::create(['codigo' => 'efectivo', 'nombre' => 'Efectivo', 'es_efectivo' => true, 'orden' => 1, 'activo' => true]);
        $nequi = MetodoPago::create(['codigo' => 'nequi', 'nombre' => 'Nequi', 'es_efectivo' => false, 'orden' => 2, 'activo' => true]);

        $turno = TurnoCaja::create([
            'user_apertura_id' => $admin->id,
            'abierto_en' => now(),
            'base_inicial' => 0,
        ]);

        // Venta en efectivo: recibe 12.000, total 10.000, devuelve 2.000 de cambio
        // => ingreso neto en efectivo = 10.000
        $ventaEfvo = Venta::create([
            'turno_caja_id' => $turno->id, 'user_id' => $admin->id,
            'total' => 10_000, 'efectivo_recibido' => 12_000, 'cambio' => 2_000,
        ]);
        $ventaEfvo->pagos()->create(['metodo_pago_id' => $efectivo->id, 'monto' => 12_000]);

        // Venta por Nequi: 5.000 (sin cambio)
        $ventaNequi = Venta::create([
            'turno_caja_id' => $turno->id, 'user_id' => $admin->id,
            'total' => 5_000, 'efectivo_recibido' => 0, 'cambio' => 0,
        ]);
        $ventaNequi->pagos()->create(['metodo_pago_id' => $nequi->id, 'monto' => 5_000]);

        // Gasto de caja en efectivo: valor 3.000 + ahorro 1.000 (el ahorro cuenta como egreso)
        Gasto::create([
            'turno_caja_id' => $turno->id, 'user_id' => $admin->id, 'tipo' => 'general',
            'metodo_pago_id' => $efectivo->id, 'valor' => 3_000, 'ahorro' => 1_000, 'observacion' => 'test',
        ]);

        // Gasto de mercado por Nequi: 2.000
        $tipo = TipoProductoMercado::create(['nombre' => 'Verduras', 'slug' => 'verduras']);
        $producto = ProductoMercado::create(['nombre' => 'Tomate', 'unidad_empaque' => 'kg', 'tipo_id' => $tipo->id]);
        RegistroMercado::create([
            'producto_mercado_id' => $producto->id, 'cantidad' => 1, 'valor' => 2_000,
            'metodo_pago_id' => $nequi->id, 'turno_caja_id' => $turno->id,
        ]);

        // Pago de nómina por Nequi: 2.500
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
            'monto' => 2_500, 'fecha_pago' => now()->toDateString(),
        ]);

        // Gasto fijo en efectivo: 1.500
        $concepto = ConceptoGastoFijo::create(['nombre' => 'Arriendo', 'orden' => 1, 'activo' => true]);
        GastoFijo::create([
            'concepto_gasto_fijo_id' => $concepto->id, 'metodo_pago_id' => $efectivo->id,
            'user_id' => $admin->id, 'valor' => 1_500, 'fecha' => now()->toDateString(),
        ]);

        return [$efectivo, $nequi];
    }

    public function test_consolida_ingresos_y_egresos_por_metodo(): void
    {
        $admin = $this->admin();
        $this->escenario($admin);

        $resumen = app(ConsolidadoContableService::class)
            ->resumen(now()->startOfMonth(), now()->endOfMonth());

        $this->assertSame(15_000, $resumen['totalIngresos']);
        $this->assertSame(10_000, $resumen['totalEgresos']);
        $this->assertSame(5_000, $resumen['neto']);

        $this->assertSame([
            'caja' => 4_000, 'mercado' => 2_000, 'nomina' => 2_500, 'fijos' => 1_500,
        ], $resumen['egresosPorModulo']);

        $efvo = $resumen['porMetodo']->firstWhere('nombre', 'Efectivo');
        $this->assertSame(10_000, $efvo['ingresos']);
        $this->assertSame(5_500, $efvo['egresos']);
        $this->assertSame(4_500, $efvo['neto']);

        $nequi = $resumen['porMetodo']->firstWhere('nombre', 'Nequi');
        $this->assertSame(5_000, $nequi['ingresos']);
        $this->assertSame(4_500, $nequi['egresos']);
        $this->assertSame(500, $nequi['neto']);
    }

    public function test_egreso_sin_metodo_va_a_su_propia_fila(): void
    {
        $admin = $this->admin();
        $turno = TurnoCaja::create(['user_apertura_id' => $admin->id, 'abierto_en' => now(), 'base_inicial' => 0]);

        // Gasto de caja SIN método de pago (columna nullable, registros antiguos)
        Gasto::create([
            'turno_caja_id' => $turno->id, 'user_id' => $admin->id, 'tipo' => 'general',
            'metodo_pago_id' => null, 'valor' => 7_000, 'ahorro' => 0, 'observacion' => 'sin metodo',
        ]);

        $resumen = app(ConsolidadoContableService::class)
            ->resumen(now()->startOfMonth(), now()->endOfMonth());

        $sinMetodo = $resumen['porMetodo']->firstWhere('nombre', 'Sin método');
        $this->assertNotNull($sinMetodo);
        $this->assertSame(7_000, $sinMetodo['egresos']);
        $this->assertSame(7_000, $resumen['totalEgresos']);
    }

    public function test_mercado_vinculado_a_caja_no_se_cuenta_dos_veces(): void
    {
        $admin = $this->admin();
        $nequi = MetodoPago::create(['codigo' => 'nequi', 'nombre' => 'Nequi', 'es_efectivo' => false, 'orden' => 1, 'activo' => true]);
        $turno = TurnoCaja::create(['user_apertura_id' => $admin->id, 'abierto_en' => now(), 'base_inicial' => 0]);

        $tipo = TipoProductoMercado::create(['nombre' => 'Verduras', 'slug' => 'verduras']);
        $producto = ProductoMercado::create(['nombre' => 'Tomate', 'unidad_empaque' => 'kg', 'tipo_id' => $tipo->id]);

        // Registro de mercado VINCULADO al turno de caja.
        RegistroMercado::create([
            'producto_mercado_id' => $producto->id, 'cantidad' => 1, 'valor' => 9_000,
            'metodo_pago_id' => $nequi->id, 'turno_caja_id' => $turno->id,
        ]);

        $resumen = app(ConsolidadoContableService::class)
            ->resumen(now()->startOfMonth(), now()->endOfMonth());

        // Se cuenta UNA sola vez (como egreso de mercado); caja NO lo duplica.
        $this->assertSame(9_000, $resumen['egresosPorModulo']['mercado']);
        $this->assertSame(0, $resumen['egresosPorModulo']['caja']);
        $this->assertSame(9_000, $resumen['totalEgresos']);
    }

    public function test_filtro_por_defecto_es_el_mes_actual_y_renderiza(): void
    {
        $admin = $this->admin();
        $this->escenario($admin);

        $this->actingAs($admin)->get(route('consolidado.index'))
            ->assertOk()
            ->assertViewHas('totalIngresos', 15_000)
            ->assertViewHas('totalEgresos', 10_000)
            ->assertViewHas('neto', 5_000);
    }

    public function test_movimientos_fuera_del_rango_no_se_cuentan(): void
    {
        $admin = $this->admin();
        $this->escenario($admin);

        // Rango de un mes pasado sin movimientos.
        $resumen = app(ConsolidadoContableService::class)->resumen(
            now()->subMonthsNoOverflow(3)->startOfMonth(),
            now()->subMonthsNoOverflow(3)->endOfMonth(),
        );

        $this->assertSame(0, $resumen['totalIngresos']);
        $this->assertSame(0, $resumen['totalEgresos']);
        $this->assertTrue($resumen['porMetodo']->isEmpty());
    }
}
