<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Galería. Sin imagen = espacio reservado ("Foto del local — próximamente") con el título como texto.
        Schema::create('landing_galeria_fotos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 80);
            $table->string('imagen')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_galeria_fotos');
    }
};
