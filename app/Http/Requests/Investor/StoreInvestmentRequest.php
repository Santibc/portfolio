<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ya protegido por middleware de rol y KYC
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $proyecto = $this->route('proyecto');

        // Calcular monto máximo disponible
        $montoRestante = $proyecto->monto_objetivo - $proyecto->monto_recaudado;
        $maxPermitido = min($proyecto->inversion_maxima, $montoRestante);

        return [
            'monto' => [
                'required',
                'numeric',
                'min:' . $proyecto->inversion_minima,
                'max:' . $maxPermitido,
            ],
            'acepto_terminos' => 'required|accepted',
            'firma_digital' => 'required|string|min:3|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $proyecto = $this->route('proyecto');

        return [
            'monto.required' => 'Debe ingresar el monto a invertir.',
            'monto.numeric' => 'El monto debe ser un número válido.',
            'monto.min' => 'El monto mínimo de inversión para este proyecto es $' .
                number_format($proyecto->inversion_minima, 0, ',', '.'),
            'monto.max' => 'El monto máximo de inversión es $' .
                number_format(min($proyecto->inversion_maxima, $proyecto->monto_objetivo - $proyecto->monto_recaudado), 0, ',', '.'),
            'acepto_terminos.required' => 'Debe aceptar los términos y condiciones del contrato.',
            'acepto_terminos.accepted' => 'Debe aceptar los términos y condiciones del contrato.',
            'firma_digital.required' => 'Debe firmar el contrato con su nombre completo.',
            'firma_digital.min' => 'La firma debe tener al menos 3 caracteres.',
            'firma_digital.max' => 'La firma no puede exceder 255 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'monto' => 'monto de inversión',
            'acepto_terminos' => 'aceptación de términos',
            'firma_digital' => 'firma digital',
        ];
    }
}
