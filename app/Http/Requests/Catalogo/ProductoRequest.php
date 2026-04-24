<?php

namespace App\Http\Requests\Catalogo;

use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
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
        $producto = $this->route('producto');
        $id = $producto instanceof Producto ? $producto->getKey() : null;

        return [
            'referencia' => ['required', 'string', 'max:40', Rule::unique('productos', 'referencia')->ignore($id)],
            'descripcion' => ['required', 'string', 'max:150'],
            'color' => ['nullable', 'string', 'max:60'],
            'composicion' => ['nullable', 'string', 'max:255'],
            'codigo_pa' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\.]+$/'],
            'precio_unitario' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'moneda_id' => ['required', 'integer', 'exists:monedas,id'],
            'impuesto_id' => ['nullable', 'integer', 'exists:impuestos,id'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'imagen' => ['nullable', 'image', 'mimetypes:image/png,image/jpeg,image/webp', 'max:2048'],
            'es_prenda' => ['sometimes', 'boolean'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'referencia' => strtoupper((string) $this->input('referencia')),
            'es_prenda' => $this->boolean('es_prenda'),
            'activo' => $this->boolean('activo', true),
        ]);
    }
}
