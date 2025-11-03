<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importaciones_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre_archivo');
            $table->string('ruta_archivo')->nullable();
            $table->enum('estado', ['procesando', 'completado', 'error'])->default('procesando');
            $table->integer('total_filas')->default(0);
            $table->integer('productos_creados')->default(0);
            $table->integer('productos_fallidos')->default(0);
            $table->json('errores')->nullable();
            $table->json('detalles_procesados')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importaciones_productos');
    }
};
