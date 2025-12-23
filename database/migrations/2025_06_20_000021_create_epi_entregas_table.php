<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('epi_entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epi_inventario_id')->constrained('epi_inventario');
            $table->foreignId('trabajador_id')->constrained('trabajadores');
            $table->date('fecha_entrega');
            $table->date('fecha_devolucion')->nullable();
            $table->string('motivo_devolucion', 255)->nullable();
            $table->string('firma_trabajador_path', 500)->nullable();
            $table->foreignId('entregado_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epi_entregas');
    }
};
