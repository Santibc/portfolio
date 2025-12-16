<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProjectImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verificar que el usuario es el dueño del proyecto
        $proyecto = $this->route('proyecto');
        return $proyecto && $proyecto->agricultor_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'imagen' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB
            ],
            'titulo' => [
                'nullable',
                'string',
                'max:200',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],
            'es_principal' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'imagen.required' => 'Debe seleccionar una imagen.',
            'imagen.file' => 'El archivo no es válido.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no puede superar los 2MB.',
            'titulo.max' => 'El título no puede superar los 200 caracteres.',
            'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'imagen' => 'imagen',
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'es_principal' => 'imagen principal',
        ];
    }
}
