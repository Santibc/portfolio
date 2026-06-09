<?php

namespace App\Http\Requests\Catalogo;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
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
        return [
            'tipo' => ['required', 'in:nacional,internacional'],
            'tipo_identificacion' => ['nullable', 'string', 'max:20'],
            'identificacion' => ['nullable', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:200'],
            'nombre_comercial' => ['nullable', 'string', 'max:200'],
            'email' => ['nullable', 'email:rfc', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'direccion_facturacion' => ['nullable', 'string', 'max:255'],
            'direccion_envio' => ['nullable', 'string', 'max:255'],
            'pais' => ['required', 'string', 'max:80'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'moneda_preferida_id' => ['nullable', 'integer', 'exists:monedas,id'],
            'incoterm_id' => ['nullable', 'integer', 'exists:incoterms,id'],
            'puerto_id' => ['nullable', 'integer', 'exists:puertos,id'],
            'tipo_pago_id' => ['nullable', 'integer', 'exists:tipos_pago,id'],
            'plantilla_factura_id' => ['nullable', 'integer', 'exists:plantillas_factura,id'],
            'datos_bancarios_destino' => ['nullable', 'string', 'max:2000'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo', true)]);
    }
}
