<?php

declare(strict_types=1);

namespace App\Http\Requests;

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
            'cantidad' => ['required', 'numeric', 'min:0.01', 'max:99999', 'decimal:0,2'],
            'valor'    => ['required', 'integer', 'min:1', 'max:999999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.min'     => 'La cantidad debe ser mayor a 0.',
            'cantidad.decimal' => 'La cantidad admite hasta 2 decimales.',
            'valor.min'    => 'El valor debe ser mayor a 0.',
        ];
    }
}
