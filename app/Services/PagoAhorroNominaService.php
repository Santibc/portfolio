<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Empleado;
use App\Models\PagoAhorroNomina;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class PagoAhorroNominaService
{
    /**
     * Registra la entrega de ahorro a un empleado, validando que no supere el
     * ahorro acumulado disponible (recalculado dentro de la transacción).
     *
     * @param  array{monto:int|string,observacion?:string|null,pagado_en?:string|null}  $datos
     */
    public function registrar(Empleado $empleado, array $datos, int $userId): PagoAhorroNomina
    {
        $monto = (int) $datos['monto'];

        return DB::transaction(function () use ($empleado, $datos, $monto, $userId): PagoAhorroNomina {
            $aportado = (int) $empleado->detalles()->sum('ahorro');
            $pagado = (int) $empleado->pagosAhorroNomina()->sum('monto');
            $acumulado = $aportado - $pagado;

            if ($monto > $acumulado) {
                throw new DomainException(
                    'La entrega ('.$this->formato($monto).') supera el ahorro acumulado ('.$this->formato($acumulado).').'
                );
            }

            return PagoAhorroNomina::create([
                'empleado_id' => $empleado->id,
                'user_id' => $userId,
                'monto' => $monto,
                'observacion' => $datos['observacion'] ?? null,
                'pagado_en' => isset($datos['pagado_en'])
                    ? Carbon::parse((string) $datos['pagado_en'])->toDateString()
                    : Carbon::now()->toDateString(),
            ]);
        });
    }

    public function eliminar(PagoAhorroNomina $pago): void
    {
        $pago->delete();
    }

    private function formato(int $valor): string
    {
        return '$ '.number_format($valor, 0, ',', '.');
    }
}
