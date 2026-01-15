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
        Schema::create('trabajador_bonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('cascade');
            $table->foreignId('obra_id')->nullable()->constrained('obras')->onDelete('set null');
            $table->enum('tipo', ['prima_produccion', 'bono_especial', 'plus_nocturnidad', 'otro']);
            $table->string('concepto', 255);
            $table->date('fecha');
            $table->decimal('importe', 10, 2);
            $table->boolean('pagado')->default(false);
            $table->date('fecha_pago')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            // Índices
            $table->index(['trabajador_id', 'pagado'], 'idx_trabajador_pagado');
            $table->index('fecha', 'idx_fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trabajador_bonos');
    }
};
