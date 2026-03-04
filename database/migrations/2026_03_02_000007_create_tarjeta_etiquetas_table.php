<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjeta_etiquetas', function (Blueprint $table) {
            $table->foreignId('tarjeta_id')->constrained('tarjetas')->cascadeOnDelete();
            $table->foreignId('etiqueta_id')->constrained('tablero_etiquetas')->cascadeOnDelete();

            $table->primary(['tarjeta_id', 'etiqueta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjeta_etiquetas');
    }
};
