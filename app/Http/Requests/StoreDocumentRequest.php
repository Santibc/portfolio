<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:51200', // 50MB max
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del documento es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.max' => 'La descripción no puede exceder los 5000 caracteres.',
            'file.required' => 'Debe seleccionar un archivo.',
            'file.file' => 'Debe subir un archivo válido.',
            'file.mimes' => 'El archivo debe ser de tipo: PDF, Word, Excel o PowerPoint.',
            'file.max' => 'El archivo no puede superar los 50MB.',
        ];
    }
}
