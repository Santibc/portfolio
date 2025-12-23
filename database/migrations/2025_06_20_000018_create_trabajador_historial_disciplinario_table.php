<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajador_historial_disciplinario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('tipo', ['amonestacion_verbal', 'amonestacion_escrita', 'sancion_leve', 'sancion_grave', 'sancion_muy_grave']);
            $table->text('descripcion');
            $table->string('documento_path', 500)->nullable();
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajador_historial_disciplinario');
    }
};
