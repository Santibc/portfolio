<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ConceptoGastoFijo;
use App\Models\GastoFijo;
use App\Models\MetodoPago;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GastoFijoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    private function concepto(string $nombre = 'Arriendo'): ConceptoGastoFijo
    {
        return ConceptoGastoFijo::create(['nombre' => $nombre, 'orden' => 1, 'activo' => true]);
    }

    private function metodo(): MetodoPago
    {
        return MetodoPago::create([
            'codigo' => 'nequi', 'nombre' => 'Nequi', 'es_efectivo' => false, 'orden' => 1, 'activo' => true,
        ]);
    }

    public function test_admin_puede_registrar_un_gasto_fijo(): void
    {
        $admin = $this->admin();
        $concepto = $this->concepto();
        $metodo = $this->metodo();

        $this->actingAs($admin)->post(route('gastos-fijos.store'), [
            'concepto_gasto_fijo_id' => $concepto->id,
            'metodo_pago_id' => $metodo->id,
            'valor' => 1_500_000,
            'fecha' => now()->toDateString(),
            'observacion' => 'Arriendo de junio',
        ])->assertRedirect(route('gastos-fijos.index'));

        $this->assertDatabaseHas('gastos_fijos', [
            'concepto_gasto_fijo_id' => $concepto->id,
            'metodo_pago_id' => $metodo->id,
            'valor' => 1_500_000,
            'user_id' => $admin->id,
        ]);
    }

    public function test_validacion_requiere_concepto_metodo_valor_y_fecha(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('gastos-fijos.store'), [
            'observacion' => 'sin datos',
        ])->assertSessionHasErrors(['concepto_gasto_fijo_id', 'metodo_pago_id', 'valor', 'fecha']);

        $this->assertDatabaseCount('gastos_fijos', 0);
    }

    public function test_admin_puede_editar_y_eliminar_un_gasto_fijo(): void
    {
        $admin = $this->admin();
        $concepto = $this->concepto();
        $metodo = $this->metodo();

        $gasto = GastoFijo::create([
            'concepto_gasto_fijo_id' => $concepto->id,
            'metodo_pago_id' => $metodo->id,
            'user_id' => $admin->id,
            'valor' => 100_000,
            'fecha' => now()->toDateString(),
        ]);

        $this->actingAs($admin)->put(route('gastos-fijos.update', $gasto), [
            'concepto_gasto_fijo_id' => $concepto->id,
            'metodo_pago_id' => $metodo->id,
            'valor' => 250_000,
            'fecha' => now()->toDateString(),
        ])->assertRedirect(route('gastos-fijos.index'));

        $this->assertSame(250_000, (int) $gasto->fresh()->valor);

        $this->actingAs($admin)->delete(route('gastos-fijos.destroy', $gasto))
            ->assertRedirect(route('gastos-fijos.index'));

        $this->assertSoftDeleted('gastos_fijos', ['id' => $gasto->id]);
    }

    public function test_concepto_con_gastos_no_se_puede_eliminar(): void
    {
        $admin = $this->admin();
        $concepto = $this->concepto();
        $metodo = $this->metodo();

        GastoFijo::create([
            'concepto_gasto_fijo_id' => $concepto->id,
            'metodo_pago_id' => $metodo->id,
            'user_id' => $admin->id,
            'valor' => 50_000,
            'fecha' => now()->toDateString(),
        ]);

        $this->actingAs($admin)->delete(route('gastos-fijos.conceptos.destroy', $concepto))
            ->assertRedirect(route('gastos-fijos.conceptos.index'));

        $this->assertDatabaseHas('conceptos_gasto_fijo', ['id' => $concepto->id, 'deleted_at' => null]);
    }

    public function test_usuario_sin_rol_admin_no_accede(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('gastos-fijos.index'))->assertForbidden();
        $this->actingAs($user)->get(route('gastos-fijos.conceptos.index'))->assertForbidden();
        $this->actingAs($user)->get(route('consolidado.index'))->assertForbidden();
    }

    public function test_paginas_del_modulo_renderizan(): void
    {
        $admin = $this->admin();
        $this->concepto();

        foreach ([
            route('gastos-fijos.index'),
            route('gastos-fijos.create'),
            route('gastos-fijos.conceptos.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
