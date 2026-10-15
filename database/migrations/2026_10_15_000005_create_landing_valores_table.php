<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tarjetas de "Por qué elegirnos". `icono` = App\Enums\IconoLanding.
        Schema::create('landing_valores', function (Blueprint $table) {
            $table->id();
            $table->string('icono', 30);
            $table->string('titulo', 40);
            $table->string('texto', 200);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_valores');
    }
};
