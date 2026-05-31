<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\TipoGasto;
use App\Models\Gasto;
use App\Models\TrabajadorTurno;
use App\Models\TurnoCaja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AhorroTrabajadorTest extends TestCase
{
    use RefreshDatabase;

    private function turnoAbierto(User $user): TurnoCaja
    {
        return TurnoCaja::create([
            'user_apertura_id' => $user->id,
            'abierto_en' => now(),
            'base_inicial' => 100000,
        ]);
    }

    public function test_un_gasto_de_turno_con_ahorro_descuenta_valor_y_ahorro_de_la_caja(): void
    {
        $user = User::factory()->create();
        $turno = $this->turnoAbierto($user);
        $trab = TrabajadorTurno::create([
            'nombre' => 'Tester', 'valor_turno_default' => 40000, 'valor_ahorro_default' => 20000, 'activo' => true,
        ]);

        $esperadoAntes = $turno->fresh()->load('gastos', 'ventas')->efectivo_esperado; // 100000

        $this->actingAs($user)->post(route('gastos.store'), [
            'tipo' => TipoGasto::Turno->value,
            'trabajador_turno_id' => $trab->id,
            'valor' => 40000,
            'ahorro' => 20000,
        ])->assertRedirect(route('gastos.index'));

        $this->assertDatabaseHas('gastos', [
            'trabajador_turno_id' => $trab->id,
            'valor' => 40000,
            'ahorro' => 20000,
        ]);

        $turno = $turno->fresh()->load('gastos', 'ventas');
        $this->assertSame($esperadoAntes - 60000, $turno->efectivo_esperado);
        $this->assertSame(20000, $turno->total_ahorros);
        $this->assertSame(20000, $trab->fresh()->ahorro_acumulado);
    }

    public function test_pagar_ahorro_reduce_el_acumulado_y_no_toca_la_caja(): void
    {
        $user = User::factory()->create();
        $turno = $this->turnoAbierto($user);
        $trab = TrabajadorTurno::create([
            'nombre' => 'Tester', 'valor_turno_default' => 40000, 'valor_ahorro_default' => 20000, 'activo' => true,
        ]);

        Gasto::create([
            'turno_caja_id' => $turno->id, 'user_id' => $user->id, 'tipo' => TipoGasto::Turno->value,
            'trabajador_turno_id' => $trab->id, 'valor' => 40000, 'ahorro' => 20000,
        ]);

        $this->assertSame(20000, $trab->fresh()->ahorro_acumulado);
        $esperadoCaja = $turno->fresh()->load('gastos', 'ventas')->efectivo_esperado;

        $this->actingAs($user)->post(route('pagos-ahorros.store'), [
            'trabajador_turno_id' => $trab->id,
            'monto' => 15000,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('pagos_ahorro', ['trabajador_turno_id' => $trab->id, 'monto' => 15000]);
        $this->assertSame(5000, $trab->fresh()->ahorro_acumulado);
        // La caja no cambia por pagar el ahorro (ledger-only)
        $this->assertSame($esperadoCaja, $turno->fresh()->load('gastos', 'ventas')->efectivo_esperado);
    }

    public function test_no_se_puede_pagar_mas_del_ahorro_acumulado(): void
    {
        $user = User::factory()->create();
        $turno = $this->turnoAbierto($user);
        $trab = TrabajadorTurno::create([
            'nombre' => 'Tester', 'valor_turno_default' => 40000, 'valor_ahorro_default' => 10000, 'activo' => true,
        ]);

        Gasto::create([
            'turno_caja_id' => $turno->id, 'user_id' => $user->id, 'tipo' => TipoGasto::Turno->value,
            'trabajador_turno_id' => $trab->id, 'valor' => 40000, 'ahorro' => 10000,
        ]);

        $this->actingAs($user)->post(route('pagos-ahorros.store'), [
            'trabajador_turno_id' => $trab->id,
            'monto' => 50000,
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('pagos_ahorro', ['trabajador_turno_id' => $trab->id, 'monto' => 50000]);
        $this->assertSame(10000, $trab->fresh()->ahorro_acumulado);
    }
}
