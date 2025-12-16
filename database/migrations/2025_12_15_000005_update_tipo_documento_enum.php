<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Actualizar ENUM con nuevos tipos de documentos v2.0
        DB::statement("ALTER TABLE documentos_proyecto MODIFY COLUMN tipo_documento ENUM(
            'escritura',
            'certificado_camara',
            'cedula_catastral',
            'plan_cultivo',
            'estudio_suelos',
            'licencia_ambiental',
            'poliza_seguro',
            'contrato_compra',
            'foto_terreno',
            'documento_identidad',
            'nit',
            'certificado_bpa',
            'certificado_ica',
            'certificado_invima',
            'documento_tenencia_tierra',
            'contrato_arriendo',
            'permiso_uso_tierra',
            'certificaciones_asociacion',
            'seguro_cultivo',
            'cotizaciones_gastos',
            'video_presentacion',
            'foto_empaque',
            'foto_producto_terminado',
            'otro'
        )");
    }

    public function down()
    {
        // Revertir al ENUM original
        DB::statement("ALTER TABLE documentos_proyecto MODIFY COLUMN tipo_documento ENUM(
            'escritura',
            'certificado_camara',
            'cedula_catastral',
            'plan_cultivo',
            'estudio_suelos',
            'licencia_ambiental',
            'poliza_seguro',
            'contrato_compra',
            'foto_terreno',
            'otro'
        )");
    }
};
