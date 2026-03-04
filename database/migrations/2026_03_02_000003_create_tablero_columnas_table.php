<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tablero_columnas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tablero_id')->constrained('tableros')->cascadeOnDelete();
            $table->string('nombre', 255);
            $table->unsignedInteger('posicion')->default(0);
            $table->boolean('archivada')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tablero_columnas');
    }
};
