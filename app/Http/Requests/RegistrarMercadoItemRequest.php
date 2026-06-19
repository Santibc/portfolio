<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\TurnoCajaService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegistrarMercadoItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cantidad'       => ['required', 'numeric', 'min:0.01', 'max:99999', 'decimal:0,2'],
            'valor'          => ['required', 'integer', 'min:1', 'max:999999999'],
            'metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'vincular_caja'  => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->boolean('vincular_caja') && ! app(TurnoCajaService::class)->turnoActivo()) {
                $validator->errors()->add('vincular_caja', 'No hay una caja abierta para vincular este registro.');
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
        ];
    }
}
