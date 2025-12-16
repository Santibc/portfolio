<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // La autorizacion se maneja en el middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Datos del proyecto - Fase 1 (igual que admin)
            'categoria_id' => 'required|exists:categorias_proyecto,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:5000',
            'tipo_cultivo' => 'required|string|max:100',
            'area_hectareas' => 'required|numeric|min:0.1|max:99999',
            'etapa_cultivo' => 'required|in:siembra,crecimiento,cosecha,transformacion,otro',
            'ano_inicio_cultivo' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'ubicacion' => 'required|string|max:500',
            'meta_financiamiento' => 'required|numeric|min:100000',
            'plazo_meses' => 'required|integer|min:1|max:240',
            'roi_proyectado' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'categoria_id' => 'categoria',
            'nombre' => 'nombre del proyecto',
            'descripcion' => 'descripcion',
            'tipo_cultivo' => 'tipo de cultivo',
            'area_hectareas' => 'area en hectareas',
            'etapa_cultivo' => 'etapa del cultivo',
            'ano_inicio_cultivo' => 'ano de inicio',
            'ubicacion' => 'ubicacion',
            'meta_financiamiento' => 'meta de financiamiento',
            'plazo_meses' => 'plazo en meses',
            'roi_proyectado' => 'ROI proyectado',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'categoria_id.required' => 'Debe seleccionar una categoria de proyecto.',
            'categoria_id.exists' => 'La categoria seleccionada no es valida.',
            'nombre.required' => 'El nombre del proyecto es obligatorio.',
            'nombre.max' => 'El nombre no puede tener mas de 255 caracteres.',
            'descripcion.required' => 'La descripcion del proyecto es obligatoria.',
            'descripcion.max' => 'La descripcion no puede tener mas de 5000 caracteres.',
            'tipo_cultivo.required' => 'El tipo de cultivo es obligatorio.',
            'area_hectareas.required' => 'El area en hectareas es obligatoria.',
            'area_hectareas.min' => 'El area debe ser al menos 0.1 hectareas.',
            'etapa_cultivo.required' => 'La etapa del cultivo es obligatoria.',
            'etapa_cultivo.in' => 'La etapa del cultivo seleccionada no es valida.',
            'ubicacion.required' => 'La ubicacion es obligatoria.',
            'meta_financiamiento.required' => 'La meta de financiamiento es obligatoria.',
            'meta_financiamiento.min' => 'La meta de financiamiento debe ser al menos $100,000.',
            'plazo_meses.required' => 'El plazo en meses es obligatorio.',
            'plazo_meses.min' => 'El plazo debe ser al menos 1 mes.',
            'plazo_meses.max' => 'El plazo no puede ser mayor a 240 meses.',
            'roi_proyectado.required' => 'El ROI proyectado es obligatorio.',
            'roi_proyectado.max' => 'El ROI no puede ser mayor al 100%.',
        ];
    }
}
