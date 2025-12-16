<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('perfiles_agricultor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            // Tipo de persona
            $table->enum('tipo_persona', ['natural', 'juridica'])->default('natural');

            // Datos empresa (solo si tipo_persona = 'juridica')
            $table->string('nombre_empresa', 255)->nullable();
            $table->string('nit', 50)->nullable();
            $table->string('representante_legal', 255)->nullable();
            $table->text('direccion_finca')->nullable();

            // Seguros
            $table->boolean('cultivo_asegurado')->default(false);

            // Experiencia (FASE 2)
            $table->integer('anos_experiencia')->nullable();
            $table->text('formacion_capacitaciones')->nullable();
            $table->integer('cantidad_cosechas')->nullable();
            $table->text('produccion_promedio')->nullable();

            // Equipo de trabajo (FASE 2)
            $table->integer('num_personas_trabajando')->nullable();
            $table->boolean('familia_trabaja_cultivo')->default(false);
            $table->text('roles_principales')->nullable();
            $table->enum('nivel_tecnificacion', ['manual', 'semi_tecnificado', 'tecnificado'])->nullable();

            // Estado del predio (FASE 2)
            $table->boolean('tiene_riego')->default(false);
            $table->boolean('tiene_bodega')->default(false);
            $table->boolean('tiene_transformacion')->default(false);
            $table->boolean('tiene_transporte')->default(false);
            $table->text('accesibilidad')->nullable();
            $table->text('riesgos_naturales')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perfiles_agricultor');
    }
};
