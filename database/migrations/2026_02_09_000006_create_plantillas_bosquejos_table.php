<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plantillas_bosquejos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grupo_bosquejo_id')->nullable();
            $table->string('nombre', 255);
            $table->string('ruta_archivo', 500);
            $table->string('ruta_miniatura', 500)->nullable();
            $table->timestamps();

            $table->foreign('grupo_bosquejo_id')->references('id')->on('grupos_bosquejos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plantillas_bosquejos');
    }
};
