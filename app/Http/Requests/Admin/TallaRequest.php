<?php

namespace App\Http\Requests\Admin;

use App\Models\Talla;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TallaRequest extends FormRequest
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
        $talla = $this->route('talla');
        $id = $talla instanceof Talla ? $talla->getKey() : null;

        return [
            'nombre' => ['required', 'string', 'max:20', Rule::unique('tallas', 'nombre')->ignore($id)],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => strtoupper(trim((string) $this->input('nombre'))),
            'orden' => $this->input('orden') === '' || $this->input('orden') === null ? 0 : $this->input('orden'),
            'activo' => $this->boolean('activo', true),
        ]);
    }
}
