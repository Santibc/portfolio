<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjeta_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_id')->constrained('tarjetas')->cascadeOnDelete();
            $table->string('titulo', 255);
            $table->unsignedInteger('posicion')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjeta_checklists');
    }
};
