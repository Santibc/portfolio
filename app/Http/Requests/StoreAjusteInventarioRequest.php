<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAjusteInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo'     => ['required', Rule::in(['entrada', 'salida'])],
            'cantidad' => ['required', 'numeric', 'min:0.01', 'max:99999', 'decimal:0,2'],
            'motivo'   => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.min'    => 'La cantidad debe ser mayor a 0.',
            'motivo.required' => 'Indica el motivo del ajuste (ej. stock inicial, conteo físico, producto dañado).',
        ];
    }

    /** Entrada suma al stock, salida resta. */
    public function cantidadConSigno(): float
    {
        $cantidad = (float) $this->validated('cantidad');

        return $this->validated('tipo') === 'salida' ? -$cantidad : $cantidad;
    }
}
