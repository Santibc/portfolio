<?php

namespace App\Http\Requests\Admin;

use App\Models\Incoterm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncotermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Administrador') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $incoterm = $this->route('incoterm');
        $id = $incoterm instanceof Incoterm ? $incoterm->getKey() : null;

        return [
            'codigo' => ['required', 'string', 'max:4', Rule::unique('incoterms', 'codigo')->ignore($id)],
            'descripcion' => ['required', 'string', 'max:180'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => strtoupper((string) $this->input('codigo')),
            'activo' => $this->boolean('activo', true),
        ]);
    }
}
