<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductoMercadoRequest extends FormRequest
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
            'stock_inicial'  => ['nullable', 'numeric', 'min:0', 'max:99999', 'decimal:0,2'],
            'imagen'         => ['nullable', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // El stock inicial es un ajuste de inventario: igual que los ajustes, solo admin.
            if ($this->stockInicial() > 0 && ! $this->user()->isAdmin()) {
                $v->errors()->add('stock_inicial', 'Solo un administrador puede cargar stock inicial.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'stock_inicial.min'     => 'El stock inicial no puede ser negativo.',
            'stock_inicial.decimal' => 'El stock inicial admite hasta 2 decimales.',
        ];
    }

    /** Stock con el que arranca el producto; 0 si no controla inventario o no se indicó. */
    public function stockInicial(): float
    {
        if (! $this->boolean('controla_inventario')) {
            return 0.0;
        }

        return round((float) $this->input('stock_inicial', 0), 2);
    }
}
