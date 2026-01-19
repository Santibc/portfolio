<?php

namespace App\Http\Requests;

use App\Models\SolicitudCotizacion;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para cambiar el estado de una cotización (aprobar/rechazar)
 */
class CambiarEstadoSolicitudRequest extends FormRequest
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

        // Solo se pueden cambiar cotizaciones pendientes
        return auth()->check() && $solicitud->estado === SolicitudCotizacion::ESTADO_PENDIENTE;
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            'nuevo_estado' => 'required|in:aplicada,rechazada',

            // Observaciones del vendedor obligatorias al aprobar
            'observaciones_vendedor' => 'required_if:nuevo_estado,aplicada|nullable|string|max:2000',

            // Observaciones del admin opcionales
            'observaciones_admin' => 'nullable|string|max:2000',

            // Motivo de rechazo obligatorio al rechazar
            'motivo_rechazo' => 'required_if:nuevo_estado,rechazada|nullable|string|max:1000',

            // Procesar stock automáticamente (por defecto true)
            'procesar_stock' => 'nullable|boolean',
        ];
    }

    /**
     * Mensajes de error personalizados
     */
    public function messages(): array
    {
        return [
            'nuevo_estado.required' => 'Debe seleccionar un estado',
            'nuevo_estado.in' => 'El estado seleccionado no es válido',
            'observaciones_vendedor.required_if' => 'Las observaciones del vendedor son obligatorias al aprobar la cotización',
            'observaciones_vendedor.max' => 'Las observaciones no pueden exceder 2000 caracteres',
            'observaciones_admin.max' => 'Las observaciones del admin no pueden exceder 2000 caracteres',
            'motivo_rechazo.required_if' => 'El motivo de rechazo es obligatorio',
            'motivo_rechazo.max' => 'El motivo de rechazo no puede exceder 1000 caracteres',
        ];
    }

    /**
     * Nombres de atributos personalizados
     */
    public function attributes(): array
    {
        return [
            'nuevo_estado' => 'estado',
            'observaciones_vendedor' => 'observaciones del vendedor',
            'observaciones_admin' => 'observaciones del administrador',
            'motivo_rechazo' => 'motivo de rechazo',
            'procesar_stock' => 'procesar stock',
        ];
    }

    /**
     * Preparar datos antes de la validación
     */
    protected function prepareForValidation(): void
    {
        // Si no se especifica procesar_stock, por defecto es true
        if (!$this->has('procesar_stock')) {
            $this->merge(['procesar_stock' => true]);
        }
    }
}
