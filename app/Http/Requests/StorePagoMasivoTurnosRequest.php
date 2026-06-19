<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoMasivoTurnosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Conserva solo las filas marcadas para pagar antes de validar.
     * Así una fila desmarcada (trabajador ausente) no exige valor mínimo.
     */
    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);

        $items = is_array($items)
            ? array_values(array_filter($items, fn ($item) => is_array($item) && ! empty($item['pagar'])))
            : [];

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        return [
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.trabajador_turno_id' => ['required', 'integer', 'exists:trabajadores_turno,id'],
            'items.*.metodo_pago_id'      => ['required', 'integer', 'exists:metodos_pago,id'],
            'items.*.valor'               => ['required', 'integer', 'min:1', 'max:999999999'],
            'items.*.ahorro'              => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'items.*.observacion'         => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'              => 'Marca al menos un trabajador para pagar.',
            'items.min'                   => 'Marca al menos un trabajador para pagar.',
            'items.*.valor.required'      => 'Cada trabajador marcado necesita un valor a pagar.',
            'items.*.valor.min'           => 'El valor a pagar debe ser mayor a cero.',
        ];
    }
}
