<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinaria_asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquinaria_id')->constrained('maquinaria')->cascadeOnDelete();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria_asignaciones');
    }
};
