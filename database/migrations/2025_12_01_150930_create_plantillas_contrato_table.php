<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plantillas_contrato', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 200);
            $table->text('contenido');
            $table->enum('tipo_contrato', ['inversion_staking', 'inversion_ear', 'inversion_futuros', 'inversion_cross_fund', 'proyecto_agricultor', 'terminos_servicio', 'politica_privacidad']);
            $table->string('version', 20);
            $table->boolean('activo')->default(true);
            $table->date('fecha_vigencia');
            $table->date('fecha_expiracion')->nullable();
            $table->text('variables_requeridas')->nullable();
            $table->timestamps();

            $table->index('codigo');
            $table->index('tipo_contrato');
            $table->index(['activo', 'fecha_vigencia']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('plantillas_contrato');
    }
};
