<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orden_fotos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('orden_pieza_id')->nullable();
            $table->string('tipo_foto', 50);
            $table->string('ruta_archivo', 500);
            $table->string('ruta_miniatura', 500)->nullable();
            $table->unsignedBigInteger('subido_por');
            $table->boolean('aprobada')->default(false);
            $table->unsignedBigInteger('aprobada_por')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('orden_pieza_id')->references('id')->on('orden_piezas')->onDelete('set null');
            $table->foreign('subido_por')->references('id')->on('users');
            $table->foreign('aprobada_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_fotos');
    }
};
