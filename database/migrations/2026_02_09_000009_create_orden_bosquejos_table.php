<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orden_bosquejos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('plantilla_bosquejo_id')->nullable();
            $table->string('tipo_origen', 50);
            $table->string('nombre', 255);
            $table->string('ruta_archivo', 500);
            $table->string('ruta_miniatura', 500)->nullable();
            $table->integer('orden_visual')->default(0);
            $table->timestamps();

            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('plantilla_bosquejo_id')->references('id')->on('plantillas_bosquejos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_bosquejos');
    }
};
