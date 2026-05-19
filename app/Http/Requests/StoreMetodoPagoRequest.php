<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMetodoPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo'      => ['required', 'string', 'max:50', 'unique:metodos_pago,codigo'],
            'nombre'      => ['required', 'string', 'max:100', 'unique:metodos_pago,nombre'],
            'es_efectivo' => ['nullable', 'boolean'],
            'activo'      => ['nullable', 'boolean'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
