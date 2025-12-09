<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cualquier usuario autenticado puede crear notas
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|min:3|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'El contenido de la nota es obligatorio.',
            'content.min' => 'La nota debe tener al menos 3 caracteres.',
            'content.max' => 'La nota no puede exceder los 5000 caracteres.',
        ];
    }
}
