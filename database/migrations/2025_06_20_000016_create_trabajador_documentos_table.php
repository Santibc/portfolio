<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajador_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->enum('tipo', ['contrato', 'nomina', 'dni', 'ss', 'certificado_formacion', 'apto_medico', 'otro']);
            $table->string('nombre', 255);
            $table->string('archivo_path', 500);
            $table->date('fecha_documento')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->boolean('visible_trabajador')->default(false)->comment('Si el trabajador puede verlo');
            $table->boolean('requiere_lectura')->default(false)->comment('Si requiere confirmación de lectura');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajador_documentos');
    }
};
