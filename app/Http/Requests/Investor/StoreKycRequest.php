<?php

namespace App\Http\Requests\Investor;

use Illuminate\Foundation\Http\FormRequest;

class StoreKycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Inversionista');
    }

    public function rules(): array
    {
        return [
            'documento_frente' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'documento_reverso' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'selfie' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'comprobante_domicilio' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function attributes(): array
    {
        return [
            'documento_frente' => 'documento de identidad (frente)',
            'documento_reverso' => 'documento de identidad (reverso)',
            'selfie' => 'selfie con documento',
            'comprobante_domicilio' => 'comprobante de domicilio',
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'El :attribute es obligatorio.',
            '*.file' => 'El :attribute debe ser un archivo.',
            '*.mimes' => 'El :attribute debe ser un archivo de tipo: :values.',
            '*.max' => 'El :attribute no debe superar :max KB.',
        ];
    }
}
