<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectPhase1Request extends FormRequest
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
            // Datos del proyecto
            'categoria_id' => 'required|exists:categorias_proyecto,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:5000',
            'tipo_cultivo' => 'required|string|max:100',
            'area_hectareas' => 'required|numeric|min:0.1|max:99999',
            'etapa_cultivo' => 'required|in:siembra,crecimiento,cosecha,transformacion,otro',
            'ano_inicio_cultivo' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'ubicacion' => 'required|string|max:500',
            'coordenadas' => 'nullable|string|max:100',
            'meta_financiamiento' => 'required|numeric|min:100000',
            'plazo_meses' => 'required|integer|min:1|max:240',
            'roi_proyectado' => 'required|numeric|min:0|max:100',
        ];

        // Si es admin (ruta admin), agregar validación de agricultor
        if ($this->routeIs('admin.projects.registration.*')) {
            $rules = array_merge($rules, [
                'agricultor_id' => 'nullable|exists:users,id',
                'agricultor_nuevo' => 'nullable|boolean',
                // Datos del nuevo agricultor
                'agricultor_name' => 'required_if:agricultor_nuevo,1|string|max:255',
                'agricultor_email' => [
                    'required_if:agricultor_nuevo,1',
                    'nullable',
                    'email',
                    'max:255',
                    'unique:users,email'
                ],
                'agricultor_telefono' => 'nullable|string|max:20',
                'agricultor_documento_identidad' => [
                    'required_if:agricultor_nuevo,1',
                    'nullable',
                    'string',
                    'max:50',
                    'unique:users,documento_identidad'
                ],
                'agricultor_tipo_documento' => 'nullable|in:CC,CE,NIT,PASSPORT,DNI',
                'agricultor_fecha_nacimiento' => 'nullable|date|before:today',
                'agricultor_pais' => 'nullable|string|max:100',
                'agricultor_ciudad' => 'nullable|string|max:100',
                'agricultor_direccion' => 'nullable|string|max:500',
                'agricultor_foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
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
            'categoria_id.required' => 'Debe seleccionar una categoría de proyecto.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'nombre.required' => 'El nombre del proyecto es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'descripcion.required' => 'La descripción del proyecto es obligatoria.',
            'descripcion.max' => 'La descripción no puede tener más de 5000 caracteres.',
            'tipo_cultivo.required' => 'El tipo de cultivo es obligatorio.',
            'area_hectareas.required' => 'El área en hectáreas es obligatoria.',
            'area_hectareas.min' => 'El área debe ser al menos 0.1 hectáreas.',
            'etapa_cultivo.required' => 'La etapa del cultivo es obligatoria.',
            'etapa_cultivo.in' => 'La etapa del cultivo seleccionada no es válida.',
            'ubicacion.required' => 'La ubicación es obligatoria.',
            'meta_financiamiento.required' => 'La meta de financiamiento es obligatoria.',
            'meta_financiamiento.min' => 'La meta de financiamiento debe ser al menos $100,000.',
            'plazo_meses.required' => 'El plazo en meses es obligatorio.',
            'plazo_meses.min' => 'El plazo debe ser al menos 1 mes.',
            'plazo_meses.max' => 'El plazo no puede ser mayor a 240 meses.',
            'roi_proyectado.required' => 'El ROI proyectado es obligatorio.',
            'roi_proyectado.max' => 'El ROI no puede ser mayor al 100%.',
            // Mensajes agricultor
            'agricultor_name.required_if' => 'El nombre del agricultor es obligatorio al crear uno nuevo.',
            'agricultor_email.required_if' => 'El email del agricultor es obligatorio al crear uno nuevo.',
            'agricultor_email.unique' => 'Ya existe un usuario con ese email.',
            'agricultor_email.email' => 'El email del agricultor debe ser una dirección válida.',
            'agricultor_documento_identidad.required_if' => 'El documento del agricultor es obligatorio al crear uno nuevo.',
            'agricultor_documento_identidad.unique' => 'Ya existe un usuario con ese documento.',
            'agricultor_fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'agricultor_fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'agricultor_telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'agricultor_pais.max' => 'El país no puede tener más de 100 caracteres.',
            'agricultor_ciudad.max' => 'La ciudad no puede tener más de 100 caracteres.',
            'agricultor_direccion.max' => 'La dirección no puede tener más de 500 caracteres.',
            'agricultor_foto.image' => 'La foto debe ser una imagen válida.',
            'agricultor_foto.mimes' => 'La foto debe ser JPG, JPEG, PNG o WEBP.',
            'agricultor_foto.max' => 'La foto no puede superar los 2MB.',
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
            'categoria_id' => 'categoría',
            'nombre' => 'nombre del proyecto',
            'descripcion' => 'descripción',
            'tipo_cultivo' => 'tipo de cultivo',
            'area_hectareas' => 'área en hectáreas',
            'etapa_cultivo' => 'etapa del cultivo',
            'ano_inicio_cultivo' => 'año de inicio',
            'ubicacion' => 'ubicación',
            'coordenadas' => 'coordenadas GPS',
            'meta_financiamiento' => 'meta de financiamiento',
            'plazo_meses' => 'plazo en meses',
            'roi_proyectado' => 'ROI proyectado',
            'agricultor_name' => 'nombre del agricultor',
            'agricultor_email' => 'email del agricultor',
            'agricultor_documento_identidad' => 'documento del agricultor',
            'agricultor_telefono' => 'teléfono del agricultor',
            'agricultor_fecha_nacimiento' => 'fecha de nacimiento',
            'agricultor_tipo_documento' => 'tipo de documento',
            'agricultor_pais' => 'país',
            'agricultor_ciudad' => 'ciudad',
            'agricultor_direccion' => 'dirección',
        ];
    }
}
