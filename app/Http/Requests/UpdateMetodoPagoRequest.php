<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMetodoPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('metodoPago')?->id ?? $this->route('metodoPago');

        return [
            'codigo'      => ['required', 'string', 'max:50', Rule::unique('metodos_pago', 'codigo')->ignore($id)],
            'nombre'      => ['required', 'string', 'max:100', Rule::unique('metodos_pago', 'nombre')->ignore($id)],
            'es_efectivo' => ['nullable', 'boolean'],
            'activo'      => ['nullable', 'boolean'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
