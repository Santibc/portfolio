<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modificar el enum para agregar los nuevos tipos de documento
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
            'documento_tenencia',
            'certificado_agricola',
            'certificaciones_asociacion',
            'otro'
        ) NOT NULL");
    }

    public function down()
    {
        // Revertir al enum original
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
        ) NOT NULL");
    }
};
