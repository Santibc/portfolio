<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\TipoPrestacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrestacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empleado_id' => ['required', 'integer', 'exists:empleados,id'],
            'tipo' => ['required', Rule::enum(TipoPrestacion::class)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'dias' => ['required', 'integer', 'min:1', 'max:360'],
            'fondo' => ['nullable', 'string', 'max:80'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_fin.after_or_equal' => 'La fecha final debe ser igual o posterior a la inicial.',
        ];
    }
}
