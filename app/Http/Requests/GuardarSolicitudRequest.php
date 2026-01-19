<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para crear una nueva cotización
 */
class GuardarSolicitudRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            // Cliente (requerido si no hay enlace)
            'cliente_id' => 'required_without:enlace_token|exists:clientes,id',
            'enlace_token' => 'nullable|string',

            // Items de la cotización
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.variante_id' => 'nullable|exists:variantes_producto,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_manual' => 'nullable|numeric|min:0',

            // Información adicional
            'notas_cliente' => 'nullable|string|max:1000',
            'observaciones_vendedor' => 'nullable|string|max:2000',
            'valor_flete' => 'nullable|numeric|min:0',
            'descuento_total' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'cliente_id.required_without' => 'Debe seleccionar un cliente',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'items.required' => 'Debe agregar al menos un producto a la cotización',
            'items.min' => 'Debe agregar al menos un producto a la cotización',
            'items.*.producto_id.required' => 'El producto es requerido',
            'items.*.producto_id.exists' => 'Uno de los productos seleccionados no existe',
            'items.*.cantidad.required' => 'La cantidad es requerida',
            'items.*.cantidad.min' => 'La cantidad mínima es 1',
            'items.*.precio_manual.numeric' => 'El precio debe ser numérico',
            'items.*.precio_manual.min' => 'El precio no puede ser negativo',
            'notas_cliente.max' => 'Las notas no pueden exceder 1000 caracteres',
            'observaciones_vendedor.max' => 'Las observaciones no pueden exceder 2000 caracteres',
            'valor_flete.numeric' => 'El valor del flete debe ser numérico',
            'valor_flete.min' => 'El valor del flete no puede ser negativo',
            'descuento_total.numeric' => 'El descuento debe ser numérico',
            'descuento_total.min' => 'El descuento no puede ser negativo',
        ];
    }

    /**
     * Nombres de atributos personalizados
     */
    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'items' => 'productos',
            'items.*.producto_id' => 'producto',
            'items.*.variante_id' => 'variante',
            'items.*.cantidad' => 'cantidad',
            'items.*.precio_manual' => 'precio',
            'notas_cliente' => 'notas',
            'observaciones_vendedor' => 'observaciones',
            'valor_flete' => 'flete',
            'descuento_total' => 'descuento',
        ];
    }
}
