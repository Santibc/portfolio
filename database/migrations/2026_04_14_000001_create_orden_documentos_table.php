<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orden_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->string('nombre_original', 255);
            $table->string('ruta_archivo', 500);
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->default(0);
            $table->unsignedBigInteger('subido_por');
            $table->timestamp('created_at')->nullable();

            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('subido_por')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_documentos');
    }
};
