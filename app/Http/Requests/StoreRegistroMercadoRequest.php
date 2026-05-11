<?php

namespace App\Http\Requests;

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
            'cantidad'            => ['required', 'integer', 'min:1', 'max:99999'],
            'valor'               => ['required', 'integer', 'min:1', 'max:999999999'],
            'tipo_id'             => ['nullable', 'integer', 'exists:tipos_producto_mercado,id'],
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
