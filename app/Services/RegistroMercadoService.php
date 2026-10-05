<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RegistroMercado;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/** Alta, edición y borrado de compras de mercado, manteniendo el inventario en sincronía. */
class RegistroMercadoService
{
    public function __construct(private InventarioService $inventario)
    {
    }

    public function crear(array $datos, CarbonInterface $fecha, ?int $userId): RegistroMercado
    {
        return DB::transaction(function () use ($datos, $fecha, $userId) {
            $registro             = new RegistroMercado($datos);
            $registro->created_at = $fecha;
            $registro->save();

            $this->inventario->registrarCompra($registro, $userId);

            return $registro;
        });
    }

    public function actualizar(RegistroMercado $registro, array $datos): RegistroMercado
    {
        return DB::transaction(function () use ($registro, $datos) {
            $registro->update($datos);
            $this->inventario->sincronizarCompra($registro);

            return $registro;
        });
    }

    public function eliminar(RegistroMercado $registro): void
    {
        DB::transaction(function () use ($registro) {
            $this->inventario->revertirCompra($registro);
            $registro->delete();
        });
    }
}
