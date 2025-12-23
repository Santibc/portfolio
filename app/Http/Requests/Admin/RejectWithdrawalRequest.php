<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectWithdrawalRequest extends FormRequest
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
            'motivo_rechazo' => 'required|string|min:10|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'motivo_rechazo.required' => 'Debe indicar el motivo del rechazo.',
            'motivo_rechazo.min' => 'El motivo debe tener al menos 10 caracteres.',
            'motivo_rechazo.max' => 'El motivo no puede exceder 500 caracteres.',
        ];
    }
}
