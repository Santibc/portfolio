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
        Schema::create('obra_discrepancias_valoracion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->string('periodo_mes', 7); // Formato: 2025-01, 2025-02

            // Valores Manzer
            $table->decimal('importe_producido_manzer', 14, 2);
            $table->decimal('importe_validado_cuadrilla', 14, 2)->nullable();

            // Valores Cliente
            $table->decimal('importe_aceptado_cliente', 14, 2)->nullable();
            $table->date('fecha_respuesta_cliente')->nullable();

            // Discrepancia
            $table->decimal('importe_pendiente', 14, 2);
            $table->enum('estado', ['pendiente', 'parcial', 'resuelto'])->default('pendiente');

            // Metadatos
            $table->text('notas')->nullable();
            $table->string('documento_valoracion_path', 500)->nullable();
            $table->foreignId('registrado_por')->constrained('users');
            $table->date('fecha_resolucion')->nullable();

            $table->timestamps();

            // Índices
            $table->unique(['obra_id', 'periodo_mes'], 'unique_obra_periodo');
            $table->index(['obra_id', 'estado'], 'idx_obra_estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obra_discrepancias_valoracion');
    }
};
