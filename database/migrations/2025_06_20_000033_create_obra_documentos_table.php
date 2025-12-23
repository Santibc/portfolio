<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obra_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->enum('tipo', ['contrato', 'plano', 'permiso', 'acta', 'foto', 'informe', 'otro']);
            $table->string('nombre', 255);
            $table->string('archivo_path', 500);
            $table->text('descripcion')->nullable();
            $table->date('fecha_documento')->nullable();
            $table->foreignId('subido_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obra_documentos');
    }
};
