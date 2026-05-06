<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garantia_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garantia_id')->constrained('garantias')->onDelete('cascade');
            $table->string('nombre_original', 255);
            $table->string('nombre_archivo', 255);
            $table->string('ruta_relativa', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamano')->nullable();
            $table->timestamps();

            $table->index('garantia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garantia_documentos');
    }
};
