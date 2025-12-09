<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Administrador');
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'title.required' => 'El título del curso es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.max' => 'La descripción no puede exceder los 10000 caracteres.',
            'thumbnail.image' => 'El archivo debe ser una imagen.',
            'thumbnail.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'thumbnail.max' => 'La imagen no puede superar los 2MB.',
        ];
    }
}
