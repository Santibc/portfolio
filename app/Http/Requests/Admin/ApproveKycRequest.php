<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApproveKycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Administrador');
    }

    public function rules(): array
    {
        return [
            // Puede tener notas opcionales
            'notas' => 'nullable|string|max:1000',
        ];
    }
}
