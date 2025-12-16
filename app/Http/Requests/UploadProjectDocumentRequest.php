<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadProjectDocumentRequest extends FormRequest
{
    /**
     * Tipos de documento permitidos
     */
    const TIPOS_DOCUMENTO = [
        'escritura',
        'certificado_camara',
        'cedula_catastral',
        'plan_cultivo',
        'estudio_suelos',
        'licencia_ambiental',
        'poliza_seguro',
        'contrato_compra',
        'foto_terreno',
        'documento_tenencia',
        'certificado_agricola',
        'certificaciones_asociacion',
        'otro'
    ];

    public function authorize(): bool
    {
        // Verificar que el usuario es el dueño del proyecto
        $proyecto = $this->route('proyecto');
        return $proyecto && $proyecto->agricultor_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'documento' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120', // 5MB
            ],
            'tipo_documento' => [
                'required',
                'string',
                Rule::in(self::TIPOS_DOCUMENTO),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'documento.required' => 'Debe seleccionar un documento.',
            'documento.file' => 'El archivo no es válido.',
            'documento.mimes' => 'El documento debe ser PDF, DOC o DOCX.',
            'documento.max' => 'El documento no puede superar los 5MB.',
            'tipo_documento.required' => 'Debe seleccionar el tipo de documento.',
            'tipo_documento.in' => 'El tipo de documento seleccionado no es válido.',
            'descripcion.max' => 'La descripción no puede superar los 500 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'documento' => 'documento',
            'tipo_documento' => 'tipo de documento',
            'descripcion' => 'descripción',
        ];
    }

    /**
     * Obtener etiquetas legibles para tipos de documento
     */
    public static function getTiposDocumentoLabels(): array
    {
        return [
            'escritura' => 'Escritura del terreno',
            'certificado_camara' => 'Certificado Cámara de Comercio',
            'cedula_catastral' => 'Cédula Catastral',
            'plan_cultivo' => 'Plan de Cultivo',
            'estudio_suelos' => 'Estudio de Suelos',
            'licencia_ambiental' => 'Licencia Ambiental',
            'poliza_seguro' => 'Póliza de Seguro',
            'contrato_compra' => 'Contrato de Compra',
            'foto_terreno' => 'Fotografía del Terreno',
            'documento_tenencia' => 'Documento de Tenencia',
            'certificado_agricola' => 'Certificado Agrícola',
            'certificaciones_asociacion' => 'Certificaciones de la Asociación',
            'otro' => 'Otro documento',
        ];
    }
}
