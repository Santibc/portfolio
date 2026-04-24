<?php

namespace App\Http\Requests\Facturacion;

use Illuminate\Foundation\Http\FormRequest;

class FacturaRequest extends FormRequest
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
            'fecha' => ['required', 'date'],
            'vencimiento' => ['nullable', 'date', 'after_or_equal:fecha'],
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'moneda_id' => ['required', 'integer', 'exists:monedas,id'],
            'tasa_cambio' => ['nullable', 'numeric', 'min:0'],
            'plantilla_factura_id' => ['nullable', 'integer', 'exists:plantillas_factura,id'],
            'flete' => ['nullable', 'numeric', 'min:0'],
            'seguro' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'po_numero' => ['nullable', 'string', 'max:60'],
            'awb' => ['nullable', 'string', 'max:60'],
            'shipper' => ['nullable', 'string', 'max:100'],
            'es_electronica' => ['sometimes', 'boolean'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['nullable', 'integer', 'exists:productos,id'],
            'items.*.referencia' => ['required', 'string', 'max:40'],
            'items.*.descripcion' => ['required', 'string', 'max:200'],
            'items.*.color' => ['nullable', 'string', 'max:60'],
            'items.*.composicion' => ['nullable', 'string', 'max:255'],
            'items.*.codigo_pa' => ['nullable', 'string', 'max:20'],
            'items.*.cantidad' => ['required', 'numeric', 'min:0.01'],
            'items.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'items.*.descuento' => ['nullable', 'numeric', 'min:0'],
            'items.*.impuesto_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.tallas' => ['nullable', 'array'],
            'items.*.tallas.*.talla' => ['nullable', 'string', 'max:20'],
            'items.*.tallas.*.cantidad' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'es_electronica' => $this->boolean('es_electronica'),
            'flete' => $this->input('flete') === '' ? 0 : $this->input('flete', 0),
            'seguro' => $this->input('seguro') === '' ? 0 : $this->input('seguro', 0),
        ]);
    }
}
