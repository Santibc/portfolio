<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Administrador');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'video' => 'nullable|file|mimes:mp4,webm,ogg,mov,avi|max:512000', // 500MB max
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del video es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.max' => 'La descripción no puede exceder los 5000 caracteres.',
            'video.file' => 'Debe subir un archivo válido.',
            'video.mimes' => 'El video debe ser de tipo: mp4, webm, ogg, mov o avi.',
            'video.max' => 'El video no puede superar los 500MB.',
        ];
    }
}
