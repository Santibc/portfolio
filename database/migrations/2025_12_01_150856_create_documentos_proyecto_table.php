<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documentos_proyecto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->enum('tipo_documento', ['escritura', 'certificado_camara', 'cedula_catastral', 'plan_cultivo', 'estudio_suelos', 'licencia_ambiental', 'poliza_seguro', 'contrato_compra', 'foto_terreno', 'otro']);
            $table->string('nombre_archivo', 255);
            $table->string('ruta_archivo', 500);
            $table->string('tipo_mime', 100);
            $table->unsignedBigInteger('tamano_bytes');
            $table->text('descripcion')->nullable();
            $table->boolean('verificado')->default(false);
            $table->unsignedBigInteger('verificado_por')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->unsignedBigInteger('subido_por');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            $table->foreign('verificado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('subido_por')->references('id')->on('users')->onDelete('restrict');

            $table->index('proyecto_id');
            $table->index('tipo_documento');
            $table->index('verificado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documentos_proyecto');
    }
};
