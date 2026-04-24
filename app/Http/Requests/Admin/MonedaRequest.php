<?php

namespace App\Http\Requests\Admin;

use App\Models\Moneda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MonedaRequest extends FormRequest
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
        $moneda = $this->route('moneda');
        $id = $moneda instanceof Moneda ? $moneda->getKey() : null;

        return [
            'codigo' => ['required', 'string', 'size:3', Rule::unique('monedas', 'codigo')->ignore($id)],
            'nombre' => ['required', 'string', 'max:80'],
            'simbolo' => ['required', 'string', 'max:8'],
            'es_predeterminada' => ['sometimes', 'boolean'],
            'activa' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => strtoupper((string) $this->input('codigo')),
            'es_predeterminada' => $this->boolean('es_predeterminada'),
            'activa' => $this->boolean('activa', true),
        ]);
    }
}
