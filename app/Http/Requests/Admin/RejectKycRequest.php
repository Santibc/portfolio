<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectKycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Administrador');
    }

    public function rules(): array
    {
        return [
            'motivo' => 'required|string|max:1000',
        ];
    }

    public function attributes(): array
    {
        return [
            'motivo' => 'motivo del rechazo',
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'Debes proporcionar un motivo para rechazar el KYC.',
            'motivo.max' => 'El motivo no debe superar :max caracteres.',
        ];
    }
}
