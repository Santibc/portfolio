<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MarkWithdrawalPaidRequest extends FormRequest
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
        return [
            'comprobante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notas_aprobacion' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'comprobante.required' => 'Debe subir el comprobante de pago.',
            'comprobante.file' => 'El comprobante debe ser un archivo.',
            'comprobante.mimes' => 'El comprobante debe ser PDF, JPG o PNG.',
            'comprobante.max' => 'El comprobante no puede exceder 5MB.',
            'notas_aprobacion.max' => 'Las notas no pueden exceder 500 caracteres.',
        ];
    }
}
