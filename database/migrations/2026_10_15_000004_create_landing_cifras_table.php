<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cifras de "Nuestra historia" (ej. prefijo "+", valor 40, texto "productos horneados a diario").
        Schema::create('landing_cifras', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('valor');
            $table->string('prefijo', 5)->nullable();
            $table->string('sufijo', 10)->nullable();
            $table->string('texto', 80);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_cifras');
    }
};
