<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrabajadorTurnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'               => ['required', 'string', 'max:100'],
            'valor_turno_default'  => ['required', 'integer', 'min:0', 'max:999999999'],
            'valor_ahorro_default' => ['required', 'integer', 'min:0', 'max:999999999'],
            'activo'               => ['nullable', 'boolean'],
        ];
    }
}
