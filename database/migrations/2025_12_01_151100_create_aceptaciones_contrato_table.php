<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('aceptaciones_contrato', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inversion_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('plantilla_contrato_id');
            $table->text('contenido_contrato');
            $table->string('ip_aceptacion', 50);
            $table->string('user_agent', 500);
            $table->timestamp('fecha_aceptacion');
            $table->string('firma_digital', 500)->nullable();
            $table->boolean('acepto_terminos')->default(true);
            $table->timestamps();

            $table->foreign('inversion_id')->references('id')->on('inversiones')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('plantilla_contrato_id')->references('id')->on('plantillas_contrato')->onDelete('restrict');

            $table->index('inversion_id');
            $table->index('usuario_id');
            $table->index('fecha_aceptacion');
        });
    }

    public function down()
    {
        Schema::dropIfExists('aceptaciones_contrato');
    }
};
