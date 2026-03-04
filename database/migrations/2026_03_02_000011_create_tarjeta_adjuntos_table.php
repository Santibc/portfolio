<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjeta_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_id')->constrained('tarjetas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre_original', 255);
            $table->string('ruta_archivo', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamano')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjeta_adjuntos');
    }
};
