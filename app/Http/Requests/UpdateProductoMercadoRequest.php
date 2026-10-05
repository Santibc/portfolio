<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
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
            'controla_inventario' => ['nullable', 'boolean'],
            'imagen'         => ['nullable', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $producto = $this->route('producto');

            // Si caja lo descuenta, no puede dejar de controlar inventario (quedaría vendiendo "a ciegas").
            if (! $this->boolean('controla_inventario') && $producto?->componentesMenu()->exists()) {
                $v->errors()->add(
                    'controla_inventario',
                    'Este producto está vinculado a items del menú de caja. Quítalo de esos items antes de desactivar el inventario.'
                );
            }
        });
    }
}
