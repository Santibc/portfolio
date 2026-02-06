<?php

namespace App\Http\Requests;

use App\Models\SolicitudCotizacion;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para actualizar una cotización existente
 */
class ActualizarSolicitudRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado
     */
    public function authorize(): bool
    {
        $solicitud = $this->route('solicitud');

        if (!$solicitud instanceof SolicitudCotizacion) {
            return false;
        }

        // Solo se pueden editar cotizaciones pendientes
        return auth()->check() && $solicitud->esEditable();
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
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
            'porcentaje_iva' => 'nullable|numeric|in:0,5,19',
            'forma_pago_factura' => 'nullable|string|max:50',
            'dias_vencimiento' => 'nullable|integer|min:0|max:365',
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Debe haber al menos un producto en la cotización',
            'items.min' => 'Debe haber al menos un producto en la cotización',
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
            'porcentaje_iva.numeric' => 'El porcentaje de IVA debe ser numérico',
            'porcentaje_iva.in' => 'El porcentaje de IVA debe ser 0%, 5% o 19%',
            'forma_pago_factura.max' => 'La forma de pago no puede exceder 50 caracteres',
            'dias_vencimiento.integer' => 'Los días de vencimiento deben ser un número entero',
            'dias_vencimiento.min' => 'Los días de vencimiento no pueden ser negativos',
            'dias_vencimiento.max' => 'Los días de vencimiento no pueden exceder 365',
        ];
    }

    /**
     * Nombres de atributos personalizados
     */
    public function attributes(): array
    {
        return [
            'items' => 'productos',
            'items.*.producto_id' => 'producto',
            'items.*.variante_id' => 'variante',
            'items.*.cantidad' => 'cantidad',
            'items.*.precio_manual' => 'precio',
            'notas_cliente' => 'notas',
            'observaciones_vendedor' => 'observaciones',
            'valor_flete' => 'flete',
            'descuento_total' => 'descuento',
            'porcentaje_iva' => 'IVA',
        ];
    }
}
