<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GastoFijo;
use Illuminate\Support\Facades\DB;

class GastoFijoService
{
    public function crear(array $datos, int $userId): GastoFijo
    {
        return DB::transaction(fn () => GastoFijo::create([
            'concepto_gasto_fijo_id' => (int) $datos['concepto_gasto_fijo_id'],
            'metodo_pago_id' => (int) $datos['metodo_pago_id'],
            'user_id' => $userId,
            'valor' => (int) $datos['valor'],
            'fecha' => $datos['fecha'],
            'observacion' => $datos['observacion'] ?? null,
        ]));
    }

    public function actualizar(GastoFijo $gastoFijo, array $datos): GastoFijo
    {
        $gastoFijo->fill([
            'concepto_gasto_fijo_id' => (int) $datos['concepto_gasto_fijo_id'],
            'metodo_pago_id' => (int) $datos['metodo_pago_id'],
            'valor' => (int) $datos['valor'],
            'fecha' => $datos['fecha'],
            'observacion' => $datos['observacion'] ?? null,
        ])->save();

        return $gastoFijo->fresh(['concepto', 'metodoPago', 'user']);
    }

    public function eliminar(GastoFijo $gastoFijo): void
    {
        $gastoFijo->delete();
    }
}
