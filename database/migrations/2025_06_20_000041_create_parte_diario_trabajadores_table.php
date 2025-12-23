<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parte_diario_trabajadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parte_diario_id')->constrained('partes_diarios')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores');
            $table->boolean('es_aplicador')->default(false)->comment('Aplicador de herbicida');
            $table->string('dni_aplicador', 20)->nullable()->comment('Solo si es aplicador');
            $table->decimal('horas_trabajadas', 5, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parte_diario_trabajadores');
    }
};
