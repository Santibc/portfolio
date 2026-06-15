<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoNomina;
use App\Enums\EstadoPagoNomina;
use App\Models\Empleado;
use App\Models\MetodoPago;
use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NominaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    private function metodoEfectivo(): MetodoPago
    {
        return MetodoPago::create([
            'codigo' => 'efectivo', 'nombre' => 'Efectivo', 'es_efectivo' => true, 'orden' => 1, 'activo' => true,
        ]);
    }

    private function empleadoSmmlv(string $doc = '900001'): Empleado
    {
        return Empleado::create([
            'nombre' => 'Empleado Test', 'documento' => $doc, 'salario_base' => 1_750_905,
            'auxilio_transporte' => 249_095, 'tiene_auxilio' => true, 'bono_default' => 0,
            'porcentaje_salud' => 4, 'porcentaje_pension' => 4, 'fecha_ingreso' => '2025-01-01', 'activo' => true,
        ]);
    }

    public function test_liquidar_genera_una_linea_por_empleado_con_snapshot_correcto(): void
    {
        $admin = $this->admin();
        $this->empleadoSmmlv();

        $this->actingAs($admin)->post(route('nomina.store'), [
            'tipo' => 'quincenal', 'fecha_inicio' => '2026-04-16', 'fecha_fin' => '2026-04-30', 'dias' => 15,
        ])->assertRedirect();

        $this->assertDatabaseCount('nomina_detalles', 1);

        $d = NominaDetalle::first();
        $this->assertSame(875_453, $d->basico);
        $this->assertSame(124_548, $d->auxilio);
        $this->assertSame(35_018, $d->salud);
        $this->assertSame(35_018, $d->pension);
        $this->assertSame(1_000_001, $d->total_devengado);
        $this->assertSame(929_965, $d->neto);
        $this->assertSame(EstadoNomina::Borrador, $d->nomina->estado);
    }

    public function test_no_se_puede_liquidar_periodo_duplicado(): void
    {
        $admin = $this->admin();
        $this->empleadoSmmlv();

        $payload = ['tipo' => 'quincenal', 'fecha_inicio' => '2026-04-16', 'fecha_fin' => '2026-04-30', 'dias' => 15];
        $this->actingAs($admin)->post(route('nomina.store'), $payload)->assertRedirect();
        $this->actingAs($admin)->post(route('nomina.store'), $payload)->assertSessionHasErrors('fecha_inicio');

        $this->assertSame(1, Nomina::count());
    }

    public function test_pago_parcial_y_total_actualizan_estados(): void
    {
        $admin = $this->admin();
        $this->empleadoSmmlv();
        $metodo = $this->metodoEfectivo();

        $this->actingAs($admin)->post(route('nomina.store'), [
            'tipo' => 'quincenal', 'fecha_inicio' => '2026-04-16', 'fecha_fin' => '2026-04-30', 'dias' => 15,
        ]);
        $detalle = NominaDetalle::first();

        // Pago parcial
        $this->actingAs($admin)->post(route('nomina-pagos.store'), [
            'nomina_detalle_id' => $detalle->id, 'metodo_pago_id' => $metodo->id,
            'monto' => 400_000, 'fecha_pago' => '2026-04-30',
        ])->assertRedirect();

        $this->assertSame(EstadoPagoNomina::Parcial, $detalle->fresh()->estado_pago);

        // Pago del saldo restante => pagado, y la nómina pasa a pagada
        $saldo = $detalle->fresh()->saldo_pendiente;
        $this->actingAs($admin)->post(route('nomina-pagos.store'), [
            'nomina_detalle_id' => $detalle->id, 'metodo_pago_id' => $metodo->id,
            'monto' => $saldo, 'fecha_pago' => '2026-04-30',
        ])->assertRedirect();

        $this->assertSame(EstadoPagoNomina::Pagado, $detalle->fresh()->estado_pago);
        $this->assertSame(EstadoNomina::Pagada, $detalle->fresh()->nomina->estado);
    }

    public function test_no_se_puede_pagar_mas_del_saldo(): void
    {
        $admin = $this->admin();
        $this->empleadoSmmlv();
        $metodo = $this->metodoEfectivo();

        $this->actingAs($admin)->post(route('nomina.store'), [
            'tipo' => 'quincenal', 'fecha_inicio' => '2026-04-16', 'fecha_fin' => '2026-04-30', 'dias' => 15,
        ]);
        $detalle = NominaDetalle::first();

        $this->actingAs($admin)->post(route('nomina-pagos.store'), [
            'nomina_detalle_id' => $detalle->id, 'metodo_pago_id' => $metodo->id,
            'monto' => 5_000_000, 'fecha_pago' => '2026-04-30',
        ])->assertSessionHasErrors('monto');

        $this->assertDatabaseCount('pagos_nomina', 0);
    }

    public function test_usuario_sin_rol_admin_no_accede(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('nomina.index'))->assertForbidden();
        $this->actingAs($user)->get(route('empleados.index'))->assertForbidden();
    }

    public function test_se_puede_reliquidar_un_periodo_despues_de_eliminarlo(): void
    {
        $admin = $this->admin();
        $this->empleadoSmmlv();

        $payload = ['tipo' => 'quincenal', 'fecha_inicio' => '2026-06-01', 'fecha_fin' => '2026-06-15', 'dias' => 15];

        $this->actingAs($admin)->post(route('nomina.store'), $payload)->assertRedirect();
        $nomina = Nomina::first();

        $this->actingAs($admin)->delete(route('nomina.destroy', $nomina))->assertRedirect(route('nomina.index'));
        $this->assertSame(0, Nomina::count());

        // Reliquidar el MISMO período no debe chocar con el índice único.
        $this->actingAs($admin)->post(route('nomina.store'), $payload)->assertRedirect();
        $this->assertSame(1, Nomina::count());
    }

    public function test_dashboard_cuenta_prestacion_cuyo_periodo_se_solapa_con_el_rango(): void
    {
        $admin = $this->admin();
        $empleado = $this->empleadoSmmlv();

        // Prestación cuyo período se extiende más allá del "hasta" por defecto,
        // pero que se solapa con el rango (no debe perderse).
        \App\Models\PrestacionSocial::create([
            'empleado_id' => $empleado->id, 'tipo' => 'prima',
            'fecha_inicio' => now()->subMonths(2)->toDateString(),
            'fecha_fin' => now()->addMonths(2)->toDateString(),
            'dias' => 180, 'base' => 2_000_000, 'valor' => 1_700_000,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($admin)->get(route('nomina-dashboard.index'))
            ->assertOk()
            ->assertViewHas('totalPrestaciones', 1_700_000)
            ->assertViewHas('prestacionesPendientes', 1);
    }

    public function test_todas_las_paginas_de_nomina_renderizan(): void
    {
        $admin = $this->admin();
        $this->empleadoSmmlv();
        $this->metodoEfectivo();

        $this->actingAs($admin)->post(route('nomina.store'), [
            'tipo' => 'quincenal', 'fecha_inicio' => '2026-04-16', 'fecha_fin' => '2026-04-30', 'dias' => 15,
        ]);
        $nomina = Nomina::first();
        $detalle = NominaDetalle::first();

        $paginas = [
            route('nomina-dashboard.index'),
            route('empleados.index'),
            route('empleados.create'),
            route('nomina.index'),
            route('nomina.create'),
            route('nomina.show', $nomina),
            route('nomina.edit', $nomina),
            route('nomina-pagos.masivo'),
            route('nomina-pagos.create', $detalle),
            route('prestaciones.index'),
            route('prestaciones.create'),
            route('nomina-ahorros.index'),
        ];

        foreach ($paginas as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
