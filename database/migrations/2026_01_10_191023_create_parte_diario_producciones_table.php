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
        Schema::create('parte_diario_producciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parte_diario_id')->constrained('partes_diarios')->onDelete('cascade');
            $table->foreignId('concepto_produccion_id')->constrained('obra_conceptos_produccion')->onDelete('cascade');
            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio_unitario', 10, 2); // Snapshot del precio al momento
            $table->decimal('importe_calculado', 14, 2); // cantidad × precio_unitario
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índices
            $table->unique(['parte_diario_id', 'concepto_produccion_id'], 'unique_parte_concepto');
            $table->index(['parte_diario_id', 'importe_calculado'], 'idx_parte_importe');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parte_diario_producciones');
    }
};
