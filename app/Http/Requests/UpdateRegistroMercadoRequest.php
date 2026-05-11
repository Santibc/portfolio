<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistroMercadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cantidad' => ['required', 'integer', 'min:1', 'max:99999'],
            'valor'    => ['required', 'integer', 'min:1', 'max:999999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'valor.min'    => 'El valor debe ser mayor a 0.',
        ];
    }
}
