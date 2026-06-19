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
            'metodo_pago_id'      => (int) $datos['metodo_pago_id'],
            'valor'               => $valor,
            'ahorro'              => $tipo === TipoGasto::Turno ? (int) ($datos['ahorro'] ?? 0) : 0,
            'observacion'         => $datos['observacion'] ?? null,
        ]));
    }

    /**
     * Registra varios pagos de turno en una sola transacción (uno por trabajador).
     *
     * @param  array<int, array{trabajador_turno_id: int|string, metodo_pago_id: int|string, valor: int|string, ahorro?: int|string|null, observacion?: string|null}>  $items
     * @return int  Cantidad de pagos registrados.
     */
    public function crearMasivoTurnos(array $items, int $userId): int
    {
        $turno = $this->turnos->turnoActivo();
        if ($turno === null) {
            throw new DomainException('No hay un turno de caja abierto. Abre la caja para registrar los pagos.');
        }

        if ($items === []) {
            throw new DomainException('Selecciona al menos un trabajador para pagar.');
        }

        return DB::transaction(function () use ($items, $turno, $userId): int {
            foreach ($items as $item) {
                Gasto::create([
                    'turno_caja_id'       => $turno->id,
                    'user_id'             => $userId,
                    'tipo'                => TipoGasto::Turno->value,
                    'trabajador_turno_id' => (int) $item['trabajador_turno_id'],
                    'metodo_pago_id'      => (int) $item['metodo_pago_id'],
                    'valor'               => (int) $item['valor'],
                    'ahorro'              => (int) ($item['ahorro'] ?? 0),
                    'observacion'         => $item['observacion'] ?? null,
                ]);
            }

            return count($items);
        });
    }

    public function actualizar(Gasto $gasto, array $datos): Gasto
    {
        $tipo  = TipoGasto::from((string) $datos['tipo']);
        $valor = (int) $datos['valor'];

        $this->validarTipo($tipo, $datos);

        $gasto->fill([
            'tipo'                => $tipo->value,
            'trabajador_turno_id' => $tipo === TipoGasto::Turno ? (int) $datos['trabajador_turno_id'] : null,
            'metodo_pago_id'      => (int) $datos['metodo_pago_id'],
            'valor'               => $valor,
            'ahorro'              => $tipo === TipoGasto::Turno ? (int) ($datos['ahorro'] ?? 0) : 0,
            'observacion'         => $datos['observacion'] ?? null,
        ])->save();

        return $gasto->fresh(['trabajadorTurno', 'metodoPago', 'turno', 'user']);
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
