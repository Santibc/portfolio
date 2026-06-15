<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado')?->id;

        return [
            'nombre' => ['required', 'string', 'max:120'],
            'documento' => ['required', 'string', 'max:30', Rule::unique('empleados', 'documento')->ignore($empleadoId)],
            'cargo' => ['nullable', 'string', 'max:80'],
            'metodo_pago_id' => ['nullable', 'integer', 'exists:metodos_pago,id'],
            'salario_base' => ['required', 'integer', 'min:1', 'max:999999999'],
            'auxilio_transporte' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'bono_default' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'porcentaje_salud' => ['required', 'integer', 'between:0,100'],
            'porcentaje_pension' => ['required', 'integer', 'between:0,100'],
            'eps' => ['nullable', 'string', 'max:80'],
            'fondo_pension' => ['nullable', 'string', 'max:80'],
            'fondo_cesantias' => ['nullable', 'string', 'max:80'],
            'fecha_ingreso' => ['required', 'date'],
            'banco' => ['nullable', 'string', 'max:80'],
            'numero_cuenta' => ['nullable', 'string', 'max:40'],
        ];
    }

    public function messages(): array
    {
        return [
            'documento.unique' => 'Ya existe un empleado con ese documento.',
        ];
    }
}
