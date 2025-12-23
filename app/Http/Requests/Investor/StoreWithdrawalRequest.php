<?php

namespace App\Http\Requests\Investor;

use App\Models\ConfiguracionSistema;
use Illuminate\Foundation\Http\FormRequest;

class StoreWithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $minimo = (float) ConfiguracionSistema::obtenerValor('retiro_minimo', 50000);

        $rules = [
            'monto' => [
                'required',
                'numeric',
                'min:' . $minimo,
            ],
            'metodo_pago' => 'required|in:transferencia_bancaria,nequi,daviplata',
            'numero_cuenta' => 'required|string|max:50',
            'titular' => 'required|string|max:255',
            'notas' => 'nullable|string|max:500',
        ];

        // Reglas adicionales para transferencia bancaria
        if ($this->input('metodo_pago') === 'transferencia_bancaria') {
            $rules['banco'] = 'required|string|max:100';
            $rules['tipo_cuenta'] = 'required|in:ahorros,corriente';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $minimo = (float) ConfiguracionSistema::obtenerValor('retiro_minimo', 50000);

        return [
            'monto.required' => 'Debe ingresar el monto a retirar.',
            'monto.numeric' => 'El monto debe ser un número válido.',
            'monto.min' => 'El monto mínimo de retiro es $' . number_format($minimo, 0, ',', '.') . ' COP.',
            'metodo_pago.required' => 'Debe seleccionar un método de pago.',
            'metodo_pago.in' => 'El método de pago seleccionado no es válido.',
            'numero_cuenta.required' => 'Debe ingresar el número de cuenta o celular.',
            'titular.required' => 'Debe ingresar el nombre del titular de la cuenta.',
            'banco.required' => 'Debe seleccionar el banco para transferencia bancaria.',
            'tipo_cuenta.required' => 'Debe seleccionar el tipo de cuenta.',
            'tipo_cuenta.in' => 'El tipo de cuenta debe ser ahorros o corriente.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'monto' => 'monto de retiro',
            'metodo_pago' => 'método de pago',
            'numero_cuenta' => 'número de cuenta',
            'titular' => 'titular de la cuenta',
            'banco' => 'banco',
            'tipo_cuenta' => 'tipo de cuenta',
        ];
    }
}
