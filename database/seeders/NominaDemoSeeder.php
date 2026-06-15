<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\MetodoPago;
use App\Models\Nomina;
use App\Models\User;
use App\Services\LiquidacionNominaService;
use App\Services\PagoNominaService;
use Illuminate\Database\Seeder;

class NominaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::orderBy('id')->first();
        if ($user === null || Empleado::activos()->count() === 0) {
            return;
        }

        $inicio = '2026-04-16';
        $fin = '2026-04-30';

        // Idempotente: no recrear si ya existe la nómina de ese período.
        if (Nomina::whereDate('fecha_inicio', $inicio)->whereDate('fecha_fin', $fin)->exists()) {
            return;
        }

        $liquidacion = app(LiquidacionNominaService::class);
        $pagos = app(PagoNominaService::class);

        $nomina = $liquidacion->liquidar([
            'tipo' => 'quincenal',
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
            'dias' => 15,
        ], (int) $user->id);

        $metodo = MetodoPago::where('es_efectivo', true)->value('id')
            ?? MetodoPago::orderBy('orden')->value('id');

        if ($metodo === null) {
            return;
        }

        // Demo de estados de pago: la primera línea queda pagada por completo,
        // la segunda con un pago parcial.
        $detalles = $nomina->detalles()->orderBy('empleado_nombre')->get();

        if ($detalles->count() > 0) {
            $primero = $detalles->first();
            $pagos->registrar($primero, [
                'metodo_pago_id' => $metodo,
                'monto' => $primero->neto,
                'fecha_pago' => $fin,
                'referencia' => 'Pago demo completo',
            ], (int) $user->id);
        }

        if ($detalles->count() > 1) {
            $segundo = $detalles->get(1);
            $pagos->registrar($segundo, [
                'metodo_pago_id' => $metodo,
                'monto' => (int) floor($segundo->neto / 2),
                'fecha_pago' => $fin,
                'referencia' => 'Abono demo parcial',
            ], (int) $user->id);
        }
    }
}
