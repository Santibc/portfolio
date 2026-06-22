<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConceptoGastoFijoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('conceptos_gasto_fijo', 'nombre')->ignore($this->route('concepto')),
            ],
            'orden' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
