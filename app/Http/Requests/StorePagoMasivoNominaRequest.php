<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\NominaDetalle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePagoMasivoNominaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Conserva solo las filas marcadas para pagar antes de validar.
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.nomina_detalle_id' => ['required', 'integer', 'exists:nomina_detalles,id'],
            'items.*.metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'items.*.monto' => ['required', 'integer', 'min:1', 'max:999999999'],
            'items.*.fecha_pago' => ['required', 'date'],
            'items.*.referencia' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ((array) $this->input('items', []) as $i => $item) {
                $detalle = NominaDetalle::find($item['nomina_detalle_id'] ?? null);
                if ($detalle === null) {
                    continue;
                }

                if ((int) ($item['monto'] ?? 0) > $detalle->saldo_pendiente) {
                    $validator->errors()->add(
                        "items.$i.monto",
                        $detalle->empleado_nombre.': el pago supera el saldo pendiente.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Marca al menos un empleado para pagar.',
            'items.min' => 'Marca al menos un empleado para pagar.',
        ];
    }
}
