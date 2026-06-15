<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EstadoPrestacion;
use App\Enums\TipoPrestacion;
use App\Models\Empleado;
use App\Models\PrestacionSocial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrestacionService
{
    /**
     * Liquida una prestación social (prima, cesantías, intereses o vacaciones)
     * para un empleado, aplicando la fórmula legal según el tipo.
     *
     * @param  array{tipo:string,fecha_inicio:string,fecha_fin:string,dias:int|string,fondo?:string|null,observacion?:string|null}  $datos
     */
    public function liquidar(Empleado $empleado, array $datos): PrestacionSocial
    {
        $tipo = TipoPrestacion::from((string) $datos['tipo']);
        $dias = (int) $datos['dias'];
        $salario = (int) $empleado->salario_base;
        $auxilio = (bool) $empleado->tiene_auxilio ? (int) $empleado->auxilio_transporte : 0;
        $factor = (float) config('nomina.factor_intereses_cesantias', 0.12);

        [$base, $valor] = match ($tipo) {
            TipoPrestacion::Prima => [$salario + $auxilio, CalculadoraNomina::prima($salario, $auxilio, $dias)],
            TipoPrestacion::Cesantias => [$salario + $auxilio, CalculadoraNomina::cesantias($salario, $auxilio, $dias)],
            TipoPrestacion::Intereses => $this->calcularIntereses($salario, $auxilio, $dias, $factor),
            TipoPrestacion::Vacaciones => [$salario, CalculadoraNomina::vacaciones($salario, $dias)],
        };

        return DB::transaction(fn () => PrestacionSocial::create([
            'empleado_id' => $empleado->id,
            'tipo' => $tipo->value,
            'fecha_inicio' => Carbon::parse((string) $datos['fecha_inicio'])->toDateString(),
            'fecha_fin' => Carbon::parse((string) $datos['fecha_fin'])->toDateString(),
            'dias' => $dias,
            'base' => $base,
            'valor' => $valor,
            'intereses' => 0,
            'fondo' => $datos['fondo'] ?? null,
            'estado' => EstadoPrestacion::Pendiente->value,
            'observacion' => $datos['observacion'] ?? null,
        ]));
    }

    /**
     * @param  array{metodo_pago_id:int|string,fecha_pago:string}  $datos
     */
    public function marcarPagada(PrestacionSocial $prestacion, array $datos): PrestacionSocial
    {
        $prestacion->update([
            'estado' => EstadoPrestacion::Pagada->value,
            'metodo_pago_id' => (int) $datos['metodo_pago_id'],
            'fecha_pago' => Carbon::parse((string) $datos['fecha_pago'])->toDateString(),
        ]);

        return $prestacion;
    }

    public function eliminar(PrestacionSocial $prestacion): void
    {
        $prestacion->delete();
    }

    /**
     * Para los intereses: la base es la cesantía calculada y el valor es el
     * 12% anual prorrateado sobre ella.
     *
     * @return array{0:int,1:int}
     */
    private function calcularIntereses(int $salario, int $auxilio, int $dias, float $factor): array
    {
        $cesantias = CalculadoraNomina::cesantias($salario, $auxilio, $dias);
        $intereses = CalculadoraNomina::intereses($cesantias, $dias, $factor);

        return [$cesantias, $intereses];
    }
}
