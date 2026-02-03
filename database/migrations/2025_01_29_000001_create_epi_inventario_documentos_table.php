<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('epi_inventario_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epi_inventario_id')->constrained('epi_inventario')->cascadeOnDelete();
            $table->string('nombre', 255);
            $table->string('archivo_path', 500);
            $table->foreignId('subido_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epi_inventario_documentos');
    }
};
