<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // La autorización se maneja en el controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'categoria_id' => ['required', 'exists:categorias_proyecto,id'],
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['required', 'string'],
            'ubicacion' => ['required', 'string'],
            'coordenadas' => ['nullable', 'string', 'max:100'],
            'monto_objetivo' => ['required', 'numeric', 'min:0'],
            'inversion_minima' => ['required', 'numeric', 'min:0', 'lte:monto_objetivo'],
            'inversion_maxima' => ['nullable', 'numeric', 'gte:inversion_minima', 'lte:monto_objetivo'],
            'roi_anual' => ['required', 'numeric', 'between:0,100'],
            'duracion_meses' => ['required', 'integer', 'min:1', 'max:120'],
            'periodo_cosecha_meses' => ['nullable', 'integer', 'min:1', 'max:120'],
            'periodo_dividendos_dias' => ['required', 'integer', 'min:1', 'max:365'],
            'fecha_inicio_recaudacion' => ['required', 'date'],
            'fecha_cierre_recaudacion' => ['required', 'date', 'after:fecha_inicio_recaudacion'],
            'fecha_inicio_proyecto' => ['nullable', 'date', 'after:fecha_cierre_recaudacion'],
            'fecha_fin_proyecto' => ['nullable', 'date', 'after:fecha_inicio_proyecto'],
            'fecha_primer_dividendo' => ['nullable', 'date', 'after:fecha_cierre_recaudacion'],
            'nivel_riesgo' => ['required', 'in:bajo,medio,alto'],
            'datos_adicionales' => ['nullable', 'json'],
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
            'categoria_id' => 'categoría',
            'monto_objetivo' => 'monto objetivo',
            'inversion_minima' => 'inversión mínima',
            'inversion_maxima' => 'inversión máxima',
            'roi_anual' => 'ROI anual',
            'duracion_meses' => 'duración en meses',
            'periodo_cosecha_meses' => 'período de cosecha',
            'periodo_dividendos_dias' => 'período de dividendos',
            'fecha_inicio_recaudacion' => 'fecha de inicio de recaudación',
            'fecha_cierre_recaudacion' => 'fecha de cierre de recaudación',
            'fecha_inicio_proyecto' => 'fecha de inicio del proyecto',
            'fecha_fin_proyecto' => 'fecha de fin del proyecto',
            'fecha_primer_dividendo' => 'fecha del primer dividendo',
            'nivel_riesgo' => 'nivel de riesgo',
            'datos_adicionales' => 'datos adicionales',
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
            'inversion_minima.lte' => 'La inversión mínima no puede ser mayor al monto objetivo.',
            'inversion_maxima.gte' => 'La inversión máxima debe ser mayor o igual a la inversión mínima.',
            'inversion_maxima.lte' => 'La inversión máxima no puede ser mayor al monto objetivo.',
            'fecha_cierre_recaudacion.after' => 'La fecha de cierre debe ser posterior a la fecha de inicio.',
            'fecha_inicio_proyecto.after' => 'La fecha de inicio del proyecto debe ser posterior al cierre de recaudación.',
            'fecha_fin_proyecto.after' => 'La fecha de fin debe ser posterior a la fecha de inicio del proyecto.',
            'fecha_primer_dividendo.after' => 'La fecha del primer dividendo debe ser posterior al cierre de recaudación.',
        ];
    }
}
