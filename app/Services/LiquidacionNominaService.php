<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EstadoNomina;
use App\Enums\TipoPeriodo;
use App\Models\Empleado;
use App\Models\Nomina;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class LiquidacionNominaService
{
    /**
     * Crea un período de nómina y genera una línea (snapshot) por cada empleado
     * activo, calculada con el motor de nómina, dentro de una transacción.
     *
     * @param  array{tipo:string,fecha_inicio:string,fecha_fin:string,dias:int|string}  $datos
     */
    public function liquidar(array $datos, int $userId): Nomina
    {
        $empleados = Empleado::activos()->orderBy('nombre')->get();

        if ($empleados->isEmpty()) {
            throw new DomainException('No hay empleados activos. Registra al menos un empleado antes de liquidar la nómina.');
        }

        $inicio = Carbon::parse((string) $datos['fecha_inicio'])->startOfDay();
        $fin = Carbon::parse((string) $datos['fecha_fin'])->startOfDay();
        $tipo = TipoPeriodo::from((string) $datos['tipo']);
        $dias = (int) $datos['dias'];

        return DB::transaction(function () use ($empleados, $inicio, $fin, $tipo, $dias, $userId): Nomina {
            // Limpia cualquier nómina soft-deleted del mismo período: su fila física
            // aún ocupa el índice único (fecha_inicio, fecha_fin) y bloquearía el insert.
            Nomina::onlyTrashed()
                ->whereDate('fecha_inicio', $inicio->toDateString())
                ->whereDate('fecha_fin', $fin->toDateString())
                ->forceDelete();

            $nomina = Nomina::create([
                'creada_por' => $userId,
                'tipo' => $tipo->value,
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_fin' => $fin->toDateString(),
                'descripcion' => self::descripcionPeriodo($inicio, $fin),
                'dias' => $dias,
                'estado' => EstadoNomina::Borrador->value,
            ]);

            foreach ($empleados as $empleado) {
                $calc = CalculadoraNomina::liquidarLinea(
                    (int) $empleado->salario_base,
                    $dias,
                    (int) $empleado->auxilio_transporte,
                    (bool) $empleado->tiene_auxilio,
                    (int) $empleado->bono_default,
                    (int) $empleado->porcentaje_salud,
                    (int) $empleado->porcentaje_pension,
                );

                $nomina->detalles()->create([
                    'empleado_id' => $empleado->id,
                    'empleado_nombre' => $empleado->nombre,
                    'dias' => $dias,
                    'salario_base' => (int) $empleado->salario_base,
                    'auxilio' => $calc['auxilio'],
                    'bono' => $calc['bono'],
                    'porcentaje_salud' => (int) $empleado->porcentaje_salud,
                    'porcentaje_pension' => (int) $empleado->porcentaje_pension,
                    'basico' => $calc['basico'],
                    'salud' => $calc['salud'],
                    'pension' => $calc['pension'],
                    'total_devengado' => $calc['total_devengado'],
                    'total_deducido' => $calc['total_deducido'],
                    'neto' => $calc['neto'],
                    'ahorro' => 0,
                ]);
            }

            return $nomina->load('detalles.empleado');
        });
    }

    /**
     * Recalcula y guarda las líneas editadas (días/bono/auxilio/ahorro). El
     * servidor recalcula básico/salud/pensión con el motor; nunca confía en el
     * cálculo del cliente. El básico depende del salario snapshot + días.
     *
     * @param  array<int, array{id:int|string,dias:int|string,bono:int|string,auxilio:int|string,ahorro:int|string,observacion?:string|null}>  $lineas
     */
    public function actualizarLineas(Nomina $nomina, array $lineas, int $userId): Nomina
    {
        return DB::transaction(function () use ($nomina, $lineas): Nomina {
            foreach ($lineas as $linea) {
                $detalle = $nomina->detalles()->whereKey((int) $linea['id'])->first();
                if ($detalle === null) {
                    continue;
                }

                $dias = (int) $linea['dias'];
                $bono = (int) ($linea['bono'] ?? 0);
                $auxilio = (int) ($linea['auxilio'] ?? 0);
                $ahorro = (int) ($linea['ahorro'] ?? 0);

                $basico = CalculadoraNomina::basico((int) $detalle->salario_base, $dias);
                $salud = CalculadoraNomina::deduccion($basico, (int) $detalle->porcentaje_salud);
                $pension = CalculadoraNomina::deduccion($basico, (int) $detalle->porcentaje_pension);
                $devengado = CalculadoraNomina::totalDevengado($basico, $bono, $auxilio);
                $deducido = CalculadoraNomina::totalDeducido($salud, $pension);
                $neto = CalculadoraNomina::neto($devengado, $deducido);

                $detalle->update([
                    'dias' => $dias,
                    'bono' => $bono,
                    'auxilio' => $auxilio,
                    'basico' => $basico,
                    'salud' => $salud,
                    'pension' => $pension,
                    'total_devengado' => $devengado,
                    'total_deducido' => $deducido,
                    'neto' => $neto,
                    'ahorro' => min($ahorro, $neto),
                    'observacion' => $linea['observacion'] ?? null,
                ]);
            }

            return $nomina->fresh('detalles.empleado');
        });
    }

    public function aprobar(Nomina $nomina): Nomina
    {
        if ($nomina->estado === EstadoNomina::Borrador) {
            $nomina->update(['estado' => EstadoNomina::Aprobada->value]);
        }

        return $nomina;
    }

    public function eliminar(Nomina $nomina): void
    {
        DB::transaction(function () use ($nomina): void {
            // Borra primero los pagos de cada detalle para no chocar con restrictOnDelete.
            foreach ($nomina->detalles()->with('pagos')->get() as $detalle) {
                $detalle->pagos()->forceDelete();
            }
            $nomina->detalles()->forceDelete();
            // Hard delete del encabezado: una nómina soft-deleted dejaría su fila
            // ocupando el índice único (fecha_inicio, fecha_fin) e impediría volver
            // a liquidar ese mismo período.
            $nomina->forceDelete();
        });
    }

    /** "PERIODO DEL 16 AL 30 DE ABRIL DE 2026". */
    public static function descripcionPeriodo(Carbon $inicio, Carbon $fin): string
    {
        $mes = mb_strtoupper($inicio->locale('es')->monthName, 'UTF-8');

        return sprintf('PERIODO DEL %d AL %d DE %s DE %d', $inicio->day, $fin->day, $mes, $fin->year);
    }
}
