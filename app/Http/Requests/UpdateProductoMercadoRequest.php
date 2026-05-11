<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoMercadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'         => ['required', 'string', 'max:255'],
            'unidad_empaque' => ['required', 'string', 'max:50'],
            'tipo_id'        => ['required', 'integer', 'exists:tipos_producto_mercado,id'],
            'activo'         => ['nullable', 'boolean'],
            'imagen'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }
}
