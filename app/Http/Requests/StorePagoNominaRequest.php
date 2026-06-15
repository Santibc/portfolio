<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\NominaDetalle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePagoNominaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomina_detalle_id' => ['required', 'integer', 'exists:nomina_detalles,id'],
            'metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'monto' => ['required', 'integer', 'min:1', 'max:999999999'],
            'fecha_pago' => ['required', 'date'],
            'referencia' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $detalle = NominaDetalle::find($this->input('nomina_detalle_id'));
            if ($detalle === null) {
                return;
            }

            $monto = (int) $this->input('monto');
            if ($monto > $detalle->saldo_pendiente) {
                $validator->errors()->add('monto', 'El pago supera el saldo pendiente de la línea.');
            }
        });
    }
}
