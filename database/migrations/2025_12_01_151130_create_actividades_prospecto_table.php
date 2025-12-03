<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actividades_prospecto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prospecto_id');
            $table->unsignedBigInteger('usuario_id');
            $table->enum('tipo_actividad', ['llamada', 'email', 'reunion', 'whatsapp', 'visita', 'nota', 'otro']);
            $table->string('asunto', 200);
            $table->text('descripcion')->nullable();
            $table->date('fecha_actividad');
            $table->time('hora_actividad')->nullable();
            $table->enum('resultado', ['exitoso', 'sin_respuesta', 'reagendar', 'no_interesado', 'pendiente'])->nullable();
            $table->date('fecha_seguimiento')->nullable();
            $table->timestamps();

            $table->foreign('prospecto_id')->references('id')->on('prospectos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('prospecto_id');
            $table->index('usuario_id');
            $table->index('tipo_actividad');
            $table->index('fecha_actividad');
        });
    }

    public function down()
    {
        Schema::dropIfExists('actividades_prospecto');
    }
};
