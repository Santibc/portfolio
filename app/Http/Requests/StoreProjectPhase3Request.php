<?php

namespace App\Http\Requests;

use App\Models\Proyecto;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectPhase3Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            // Desglose de inversión solicitada
            'inversion_insumos' => 'nullable|numeric|min:0',
            'inversion_mano_obra' => 'nullable|numeric|min:0',
            'inversion_equipos' => 'nullable|numeric|min:0',
            'inversion_transporte' => 'nullable|numeric|min:0',
            'inversion_certificaciones' => 'nullable|numeric|min:0',
            'inversion_empaques' => 'nullable|numeric|min:0',
            'inversion_marketing' => 'nullable|numeric|min:0',

            // Proyecciones
            'produccion_estimada' => 'nullable|string|max:500',
            'precio_venta_estimado' => 'nullable|numeric|min:0',
            'canales_venta_actuales' => 'nullable|string|max:1000',
            'canales_venta_deseados' => 'nullable|string|max:1000',
            'proyeccion_ingresos' => 'nullable|string|max:2000',
            'punto_equilibrio' => 'nullable|string|max:500',
            'margen_ganancia' => 'nullable|numeric|min:0|max:100',

            // Riesgos
            'riesgo_plagas' => 'nullable|string|max:1000',
            'riesgo_clima' => 'nullable|string|max:1000',
            'riesgo_competencia' => 'nullable|string|max:1000',
            'riesgo_acceso_mercados' => 'nullable|string|max:1000',
            'riesgo_regulaciones' => 'nullable|string|max:1000',
        ];

        // Obtener el proyecto para verificar la categoría
        $proyecto = $this->route('proyecto');

        if ($proyecto && $proyecto->categoria) {
            $categoriaCodigo = $proyecto->categoria->codigo;

            // Reglas específicas para EAR
            if ($categoriaCodigo === 'EAR') {
                $rules = array_merge($rules, [
                    'earn_estado_empaque' => 'nullable|string|max:500',
                    'earn_certificaciones_pendientes' => 'nullable|array',
                    'earn_certificaciones_pendientes.*' => 'string|max:100',
                    'earn_capacidad_produccion' => 'nullable|string|max:500',
                    'earn_laboratorio_procesamiento' => 'nullable|string|max:500',
                    'earn_costos_por_unidad' => 'nullable|numeric|min:0',
                    'earn_inventario_disponible' => 'nullable|string|max:500',
                    'earn_necesidades_escalar' => 'nullable|string|max:2000',
                ]);
            }

            // Reglas específicas para FUTUROS
            if ($categoriaCodigo === 'FUTUROS') {
                $rules = array_merge($rules, [
                    'futuros_plan_expansion' => 'nullable|string|max:2000',
                    'futuros_infraestructura_requerida' => 'nullable|string|max:2000',
                    'futuros_proyeccion_3_anos' => 'nullable|string|max:2000',
                    'futuros_proyeccion_5_anos' => 'nullable|string|max:2000',
                    'futuros_amenazas_largo_plazo' => 'nullable|string|max:2000',
                    'futuros_financiacion_por_fases' => 'nullable|string|max:2000',
                ]);
            }

            // Reglas específicas para FARMING
            if ($categoriaCodigo === 'FARMING') {
                $rules = array_merge($rules, [
                    'farming_tipo_asociacion' => 'nullable|string|max:255',
                    'farming_numero_asociados' => 'nullable|integer|min:1',
                    'farming_hectareas_totales' => 'nullable|numeric|min:0',
                    'farming_commodities' => 'nullable|array',
                    'farming_commodities.*' => 'string|max:100',
                    'farming_destino_exportacion' => 'nullable|string|max:500',
                    'farming_certificaciones_exportacion' => 'nullable|array',
                    'farming_certificaciones_exportacion.*' => 'string|max:100',
                    'farming_proyeccion_dividendos' => 'nullable|string|max:2000',
                ]);
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'inversion_insumos.numeric' => 'El valor de insumos debe ser un número.',
            'inversion_insumos.min' => 'El valor de insumos no puede ser negativo.',
            'inversion_mano_obra.numeric' => 'El valor de mano de obra debe ser un número.',
            'inversion_equipos.numeric' => 'El valor de equipos debe ser un número.',
            'inversion_transporte.numeric' => 'El valor de transporte debe ser un número.',
            'inversion_certificaciones.numeric' => 'El valor de certificaciones debe ser un número.',
            'inversion_empaques.numeric' => 'El valor de empaques debe ser un número.',
            'inversion_marketing.numeric' => 'El valor de marketing debe ser un número.',
            'precio_venta_estimado.numeric' => 'El precio de venta estimado debe ser un número.',
            'margen_ganancia.numeric' => 'El margen de ganancia debe ser un número.',
            'margen_ganancia.max' => 'El margen de ganancia no puede ser mayor al 100%.',
            'farming_numero_asociados.integer' => 'El número de asociados debe ser un número entero.',
            'farming_numero_asociados.min' => 'Debe haber al menos 1 asociado.',
            'farming_hectareas_totales.numeric' => 'Las hectáreas totales deben ser un número.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'inversion_insumos' => 'inversión en insumos',
            'inversion_mano_obra' => 'inversión en mano de obra',
            'inversion_equipos' => 'inversión en equipos',
            'inversion_transporte' => 'inversión en transporte',
            'inversion_certificaciones' => 'inversión en certificaciones',
            'inversion_empaques' => 'inversión en empaques',
            'inversion_marketing' => 'inversión en marketing',
            'produccion_estimada' => 'producción estimada',
            'precio_venta_estimado' => 'precio de venta estimado',
            'canales_venta_actuales' => 'canales de venta actuales',
            'canales_venta_deseados' => 'canales de venta deseados',
            'proyeccion_ingresos' => 'proyección de ingresos',
            'punto_equilibrio' => 'punto de equilibrio',
            'margen_ganancia' => 'margen de ganancia',
            'riesgo_plagas' => 'riesgo de plagas',
            'riesgo_clima' => 'riesgo climático',
            'riesgo_competencia' => 'riesgo de competencia',
            'riesgo_acceso_mercados' => 'riesgo de acceso a mercados',
            'riesgo_regulaciones' => 'riesgo de regulaciones',
            // EARN
            'earn_estado_empaque' => 'estado del empaque',
            'earn_certificaciones_pendientes' => 'certificaciones pendientes',
            'earn_capacidad_produccion' => 'capacidad de producción',
            'earn_laboratorio_procesamiento' => 'laboratorio de procesamiento',
            'earn_costos_por_unidad' => 'costos por unidad',
            'earn_inventario_disponible' => 'inventario disponible',
            'earn_necesidades_escalar' => 'necesidades para escalar',
            // FUTUROS
            'futuros_plan_expansion' => 'plan de expansión',
            'futuros_infraestructura_requerida' => 'infraestructura requerida',
            'futuros_proyeccion_3_anos' => 'proyección a 3 años',
            'futuros_proyeccion_5_anos' => 'proyección a 5 años',
            'futuros_amenazas_largo_plazo' => 'amenazas a largo plazo',
            'futuros_financiacion_por_fases' => 'financiación por fases',
            // FARMING
            'farming_tipo_asociacion' => 'tipo de asociación',
            'farming_numero_asociados' => 'número de asociados',
            'farming_hectareas_totales' => 'hectáreas totales',
            'farming_commodities' => 'commodities',
            'farming_destino_exportacion' => 'destino de exportación',
            'farming_certificaciones_exportacion' => 'certificaciones de exportación',
            'farming_proyeccion_dividendos' => 'proyección de dividendos',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Limpiar valores vacíos de arrays
        $arrayFields = [
            'earn_certificaciones_pendientes',
            'farming_commodities',
            'farming_certificaciones_exportacion',
        ];

        foreach ($arrayFields as $field) {
            if ($this->has($field)) {
                $value = $this->input($field);
                if (is_array($value)) {
                    $this->merge([
                        $field => array_filter($value, fn($v) => !empty($v)),
                    ]);
                }
            }
        }

        // Convertir valores vacíos a null en campos numéricos
        $numericFields = [
            'inversion_insumos',
            'inversion_mano_obra',
            'inversion_equipos',
            'inversion_transporte',
            'inversion_certificaciones',
            'inversion_empaques',
            'inversion_marketing',
            'precio_venta_estimado',
            'margen_ganancia',
            'earn_costos_por_unidad',
            'farming_numero_asociados',
            'farming_hectareas_totales',
        ];

        foreach ($numericFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }
}
