<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectPhase2Request extends FormRequest
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
        return [
            // Perfil del agricultor
            'tipo_persona' => 'required|in:natural,juridica',
            'nombre_empresa' => 'nullable|required_if:tipo_persona,juridica|string|max:255',
            'nit' => 'nullable|required_if:tipo_persona,juridica|string|max:50',
            'representante_legal' => 'nullable|string|max:255',
            'direccion_finca' => 'nullable|string|max:1000',
            'cultivo_asegurado' => 'nullable|boolean',

            // Experiencia
            'anos_experiencia' => 'nullable|integer|min:0|max:100',
            'formacion_capacitaciones' => 'nullable|string|max:2000',
            'cantidad_cosechas' => 'nullable|integer|min:0',
            'produccion_promedio' => 'nullable|string|max:500',

            // Equipo de trabajo
            'num_personas_trabajando' => 'nullable|integer|min:0',
            'familia_trabaja_cultivo' => 'nullable|boolean',
            'roles_principales' => 'nullable|string|max:2000',
            'nivel_tecnificacion' => 'nullable|in:manual,semi_tecnificado,tecnificado',

            // Estado del predio
            'tiene_riego' => 'nullable|boolean',
            'tiene_bodega' => 'nullable|boolean',
            'tiene_transformacion' => 'nullable|boolean',
            'tiene_transporte' => 'nullable|boolean',
            'accesibilidad' => 'nullable|string|max:1000',
            'riesgos_naturales' => 'nullable|string|max:1000',

            // Familiares (array dinámico)
            'familia' => 'nullable|array',
            'familia.*.parentesco' => 'required_with:familia.*.nombre|in:esposa,esposo,hijo,hija,padre,madre,hermano,hermana,otro',
            'familia.*.nombre' => 'required_with:familia.*.parentesco|string|max:255',
            'familia.*.edad' => 'nullable|integer|min:0|max:150',
            'familia.*.nivel_educativo' => 'nullable|in:ninguno,primaria,secundaria,tecnico,profesional,posgrado',
            'familia.*.estudia_actualmente' => 'nullable|in:si,no,estudio_aplazado',
            'familia.*.trabaja_en_cultivo' => 'nullable|boolean',

            // Datos adicionales del proyecto
            'objetivo_proyecto' => 'nullable|string|max:5000',
            'detalle_proceso_productivo' => 'nullable|string|max:5000',
            'cronograma_estimado' => 'nullable|string|max:5000',
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
            'tipo_persona.required' => 'Debe indicar si es persona natural o jurídica.',
            'tipo_persona.in' => 'El tipo de persona debe ser natural o jurídica.',
            'nombre_empresa.required_if' => 'El nombre de la empresa es obligatorio para personas jurídicas.',
            'nit.required_if' => 'El NIT es obligatorio para personas jurídicas.',
            'anos_experiencia.integer' => 'Los años de experiencia deben ser un número entero.',
            'anos_experiencia.min' => 'Los años de experiencia no pueden ser negativos.',
            'anos_experiencia.max' => 'Los años de experiencia no pueden ser más de 100.',
            'cantidad_cosechas.integer' => 'La cantidad de cosechas debe ser un número entero.',
            'num_personas_trabajando.integer' => 'El número de personas trabajando debe ser un número entero.',
            'nivel_tecnificacion.in' => 'El nivel de tecnificación seleccionado no es válido.',
            'familia.*.parentesco.in' => 'El parentesco seleccionado no es válido.',
            'familia.*.nombre.required_with' => 'El nombre del familiar es obligatorio.',
            'familia.*.edad.integer' => 'La edad debe ser un número entero.',
            'familia.*.nivel_educativo.in' => 'El nivel educativo seleccionado no es válido.',
            'familia.*.estudia_actualmente.in' => 'La opción de estudio seleccionada no es válida.',
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
            'tipo_persona' => 'tipo de persona',
            'nombre_empresa' => 'nombre de la empresa',
            'nit' => 'NIT',
            'representante_legal' => 'representante legal',
            'direccion_finca' => 'dirección de la finca',
            'cultivo_asegurado' => 'cultivo asegurado',
            'anos_experiencia' => 'años de experiencia',
            'formacion_capacitaciones' => 'formación y capacitaciones',
            'cantidad_cosechas' => 'cantidad de cosechas',
            'produccion_promedio' => 'producción promedio',
            'num_personas_trabajando' => 'número de personas trabajando',
            'familia_trabaja_cultivo' => 'familia trabaja en el cultivo',
            'roles_principales' => 'roles principales',
            'nivel_tecnificacion' => 'nivel de tecnificación',
            'tiene_riego' => 'tiene riego',
            'tiene_bodega' => 'tiene bodega',
            'tiene_transformacion' => 'tiene transformación',
            'tiene_transporte' => 'tiene transporte',
            'accesibilidad' => 'accesibilidad',
            'riesgos_naturales' => 'riesgos naturales',
            'objetivo_proyecto' => 'objetivo del proyecto',
            'detalle_proceso_productivo' => 'detalle del proceso productivo',
            'cronograma_estimado' => 'cronograma estimado',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convertir checkboxes a booleanos
        $booleanFields = [
            'cultivo_asegurado',
            'familia_trabaja_cultivo',
            'tiene_riego',
            'tiene_bodega',
            'tiene_transformacion',
            'tiene_transporte',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }

        // Procesar familiares
        if ($this->has('familia')) {
            $familia = $this->input('familia');
            foreach ($familia as $index => $familiar) {
                if (isset($familiar['trabaja_en_cultivo'])) {
                    $familia[$index]['trabaja_en_cultivo'] = filter_var(
                        $familiar['trabaja_en_cultivo'],
                        FILTER_VALIDATE_BOOLEAN
                    );
                }
            }
            $this->merge(['familia' => $familia]);
        }
    }
}
