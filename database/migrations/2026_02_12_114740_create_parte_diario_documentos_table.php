<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parte_diario_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parte_diario_id')->constrained('partes_diarios')->cascadeOnDelete();
            $table->string('nombre', 255);
            $table->string('archivo_path', 500);
            $table->string('archivo_nombre_original', 255);
            $table->foreignId('subido_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parte_diario_documentos');
    }
};
