<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('imagenes_proyecto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->string('ruta_imagen', 500);
            $table->string('thumbnail', 500)->nullable();
            $table->string('titulo', 200)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');

            $table->index('proyecto_id');
            $table->index(['proyecto_id', 'es_principal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('imagenes_proyecto');
    }
};
