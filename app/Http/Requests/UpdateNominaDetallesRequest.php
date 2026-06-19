<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\NominaDetalle;
use App\Services\CalculadoraNomina;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateNominaDetallesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lineas' => ['required', 'array', 'min:1'],
            'lineas.*.id' => ['required', 'integer', 'exists:nomina_detalles,id'],
            'lineas.*.dias' => ['required', 'integer', 'min:1', 'max:31'],
            'lineas.*.bono' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'lineas.*.auxilio' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'lineas.*.ahorro' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'lineas.*.observacion' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ((array) $this->input('lineas', []) as $i => $linea) {
                $detalle = NominaDetalle::find($linea['id'] ?? null);
                if ($detalle === null) {
                    continue;
                }

                $dias = (int) ($linea['dias'] ?? 0);
                $bono = (int) ($linea['bono'] ?? 0);
                $auxilio = (int) ($linea['auxilio'] ?? 0);
                $ahorro = (int) ($linea['ahorro'] ?? 0);

                $basico = CalculadoraNomina::basico((int) $detalle->salario_base, $dias);
                $salud = CalculadoraNomina::deduccion($basico, (int) $detalle->porcentaje_salud);
                $pension = CalculadoraNomina::deduccion($basico, (int) $detalle->porcentaje_pension);
                $neto = CalculadoraNomina::neto(
                    CalculadoraNomina::totalDevengado($basico, $bono, $auxilio),
                    CalculadoraNomina::totalDeducido($salud, $pension)
                );

                if ($ahorro > $neto) {
                    $validator->errors()->add(
                        "lineas.$i.ahorro",
                        $detalle->empleado_nombre.': el ahorro no puede superar el neto a pagar.'
                    );
                }
            }
        });
    }
}
