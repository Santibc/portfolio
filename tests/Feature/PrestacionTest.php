<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoPrestacion;
use App\Models\Empleado;
use App\Models\MetodoPago;
use App\Models\PrestacionSocial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PrestacionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    private function empleado(): Empleado
    {
        return Empleado::create([
            'nombre' => 'Empleado Test', 'documento' => '900002', 'salario_base' => 1_750_905,
            'auxilio_transporte' => 249_095, 'tiene_auxilio' => true, 'porcentaje_salud' => 4,
            'porcentaje_pension' => 4, 'fecha_ingreso' => '2025-01-01', 'activo' => true,
        ]);
    }

    public function test_liquidar_prima_calcula_medio_sueldo_por_semestre(): void
    {
        $admin = $this->admin();
        $empleado = $this->empleado();

        $this->actingAs($admin)->post(route('prestaciones.store'), [
            'empleado_id' => $empleado->id, 'tipo' => 'prima',
            'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30', 'dias' => 180,
        ])->assertRedirect(route('prestaciones.index'));

        $prestacion = PrestacionSocial::first();
        // (1.750.905 + 249.095) * 180 / 360 = 1.000.000
        $this->assertSame(1_000_000, $prestacion->valor);
        $this->assertSame(EstadoPrestacion::Pendiente, $prestacion->estado);
    }

    public function test_marcar_prestacion_pagada(): void
    {
        $admin = $this->admin();
        $empleado = $this->empleado();
        $metodo = MetodoPago::create(['codigo' => 'efectivo', 'nombre' => 'Efectivo', 'es_efectivo' => true, 'orden' => 1, 'activo' => true]);

        $prestacion = PrestacionSocial::create([
            'empleado_id' => $empleado->id, 'tipo' => 'cesantias', 'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-12-31', 'dias' => 360, 'base' => 2_000_000, 'valor' => 2_000_000,
            'estado' => EstadoPrestacion::Pendiente->value,
        ]);

        $this->actingAs($admin)->patch(route('prestaciones.pagar', $prestacion), [
            'metodo_pago_id' => $metodo->id, 'fecha_pago' => '2026-12-20',
        ])->assertRedirect(route('prestaciones.index'));

        $this->assertSame(EstadoPrestacion::Pagada, $prestacion->fresh()->estado);
    }
}
