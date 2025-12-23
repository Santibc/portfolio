<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinaria_inspecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquinaria_id')->constrained('maquinaria')->cascadeOnDelete();
            $table->foreignId('plantilla_id')->constrained('maquinaria_checklist_plantillas');
            $table->date('fecha_inspeccion');
            $table->date('fecha_proxima_inspeccion')->nullable();
            $table->enum('resultado', ['apto', 'no_apto']);
            $table->text('observaciones')->nullable();
            $table->foreignId('realizado_por')->constrained('users');
            $table->string('firma_path', 500)->nullable();
            $table->string('documento_path', 500)->nullable()->comment('PDF generado');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria_inspecciones');
    }
};
