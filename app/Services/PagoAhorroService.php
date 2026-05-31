<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PagoAhorro;
use App\Models\TrabajadorTurno;
use DomainException;
use Illuminate\Support\Facades\DB;

class PagoAhorroService
{
    public function crear(array $datos, int $userId): PagoAhorro
    {
        $trabajador = TrabajadorTurno::findOrFail((int) $datos['trabajador_turno_id']);
        $monto = (int) $datos['monto'];

        return DB::transaction(function () use ($trabajador, $monto, $datos, $userId) {
            // Recalcula el acumulado dentro de la transacción para evitar pagos sobre saldo desactualizado.
            $aportado = (int) $trabajador->gastos()->sum('ahorro');
            $pagado = (int) $trabajador->pagosAhorro()->sum('monto');
            $acumulado = $aportado - $pagado;

            if ($monto > $acumulado) {
                throw new DomainException(
                    'El pago ('.$this->formato($monto).') supera el ahorro acumulado ('.$this->formato($acumulado).').'
                );
            }

            return PagoAhorro::create([
                'trabajador_turno_id' => $trabajador->id,
                'user_id' => $userId,
                'monto' => $monto,
                'observacion' => $datos['observacion'] ?? null,
                'pagado_en' => now(),
            ]);
        });
    }

    public function eliminar(PagoAhorro $pago): void
    {
        $pago->delete();
    }

    private function formato(int $valor): string
    {
        return '$ '.number_format($valor, 0, ',', '.');
    }
}
