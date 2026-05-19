<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipoGasto;
use App\Models\Gasto;
use DomainException;
use Illuminate\Support\Facades\DB;

class GastoService
{
    public function __construct(private TurnoCajaService $turnos)
    {
    }

    public function crear(array $datos, int $userId): Gasto
    {
        $turno = $this->turnos->turnoActivo();
        if ($turno === null) {
            throw new DomainException('No hay un turno de caja abierto. Abre la caja para registrar un gasto.');
        }

        $tipo  = TipoGasto::from((string) $datos['tipo']);
        $valor = (int) $datos['valor'];

        $this->validarTipo($tipo, $datos);

        return DB::transaction(fn () => Gasto::create([
            'turno_caja_id'       => $turno->id,
            'user_id'             => $userId,
            'tipo'                => $tipo->value,
            'trabajador_turno_id' => $tipo === TipoGasto::Turno ? (int) $datos['trabajador_turno_id'] : null,
            'valor'               => $valor,
            'observacion'         => $tipo === TipoGasto::Turno ? ($datos['observacion'] ?? null) : ($datos['observacion'] ?? null),
        ]));
    }

    public function actualizar(Gasto $gasto, array $datos): Gasto
    {
        $tipo  = TipoGasto::from((string) $datos['tipo']);
        $valor = (int) $datos['valor'];

        $this->validarTipo($tipo, $datos);

        $gasto->fill([
            'tipo'                => $tipo->value,
            'trabajador_turno_id' => $tipo === TipoGasto::Turno ? (int) $datos['trabajador_turno_id'] : null,
            'valor'               => $valor,
            'observacion'         => $datos['observacion'] ?? null,
        ])->save();

        return $gasto->fresh(['trabajadorTurno', 'turno', 'user']);
    }

    public function eliminar(Gasto $gasto): void
    {
        $gasto->delete();
    }

    private function validarTipo(TipoGasto $tipo, array $datos): void
    {
        if ($tipo === TipoGasto::Turno && empty($datos['trabajador_turno_id'])) {
            throw new DomainException('Para un pago de turno debes seleccionar un trabajador.');
        }

        if ($tipo === TipoGasto::General && empty(trim((string) ($datos['observacion'] ?? '')))) {
            throw new DomainException('Para un gasto general debes escribir una observación.');
        }
    }
}
