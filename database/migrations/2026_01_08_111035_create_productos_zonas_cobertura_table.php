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
        Schema::create('productos_zonas_cobertura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('zona_cobertura_id')->constrained('zonas_cobertura')->onDelete('cascade');
            $table->integer('stock_local')->nullable()->comment('Stock específico para esta zona (opcional)');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['producto_id', 'zona_cobertura_id'], 'producto_zona_unique');

            // Índices para búsquedas
            $table->index('producto_id');
            $table->index('zona_cobertura_id');
            $table->index(['producto_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos_zonas_cobertura');
    }
};
