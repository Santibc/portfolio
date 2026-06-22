<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGastoFijoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'concepto_gasto_fijo_id' => ['required', 'integer', 'exists:conceptos_gasto_fijo,id'],
            'metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'valor' => ['required', 'integer', 'min:1', 'max:999999999'],
            'fecha' => ['required', 'date'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ];
    }
}
