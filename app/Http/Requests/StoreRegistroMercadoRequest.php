<?php

namespace App\Http\Requests;

use App\Services\TurnoCajaService;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistroMercadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_mercado_id' => ['required', 'integer', 'exists:productos_mercado,id'],
            'cantidad'            => ['required', 'numeric', 'min:0.01', 'max:99999', 'decimal:0,2'],
            'valor'               => ['required', 'integer', 'min:1', 'max:999999999'],
            'metodo_pago_id'      => ['required', 'integer', 'exists:metodos_pago,id'],
            'fecha'               => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . today()->subYears(2)->toDateString(),
                'before_or_equal:' . today()->toDateString(),
            ],
            'vincular_caja'       => ['nullable', 'boolean'],
            'tipo_id'             => ['nullable', 'integer', 'exists:tipos_producto_mercado,id'],
            'observacion'         => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->boolean('vincular_caja')) {
                return;
            }

            if (! app(TurnoCajaService::class)->turnoActivo()) {
                $validator->errors()->add('vincular_caja', 'No hay una caja abierta para vincular este registro.');

                return;
            }

            if (! $this->esDeHoy()) {
                $validator->errors()->add('vincular_caja', 'Solo puedes vincular con la caja abierta los registros de hoy.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'cantidad.min'     => 'La cantidad debe ser mayor a 0.',
            'cantidad.decimal' => 'La cantidad admite hasta 2 decimales.',
            'valor.min'    => 'El valor debe ser mayor a 0.',
            'metodo_pago_id.required' => 'Selecciona un método de pago.',
            'fecha.required'        => 'Selecciona la fecha de la compra.',
            'fecha.date_format'     => 'La fecha no es válida.',
            'fecha.before_or_equal' => 'No puedes registrar una compra con una fecha futura.',
            'fecha.after_or_equal'  => 'La fecha es demasiado antigua (máximo 2 años atrás).',
        ];
    }

    /**
     * Fecha con la que se guarda el registro. El módulo usa `created_at` como la
     * fecha del gasto (dashboard, gráficas y consolidado filtran por ahí), así que
     * un registro atrasado conserva la hora actual pero sobre el día elegido.
     */
    public function fechaRegistro(): CarbonInterface
    {
        if ($this->esDeHoy()) {
            return now();
        }

        return $this->date('fecha', 'Y-m-d')->setTimeFrom(now());
    }

    private function esDeHoy(): bool
    {
        $fecha = $this->input('fecha');

        return ! $fecha || $fecha === today()->toDateString();
    }
}
