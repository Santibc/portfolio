<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actualizaciones_proyecto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('autor_id');
            $table->string('titulo', 200);
            $table->text('contenido');
            $table->enum('tipo', ['informativo', 'hito', 'alerta', 'financiero'])->default('informativo');
            $table->boolean('visible_inversores')->default(true);
            $table->timestamp('publicado_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            $table->foreign('autor_id')->references('id')->on('users')->onDelete('restrict');

            $table->index('proyecto_id');
            $table->index(['proyecto_id', 'publicado_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('actualizaciones_proyecto');
    }
};
