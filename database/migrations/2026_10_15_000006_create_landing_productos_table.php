<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categorías de la sección "Productos" de la landing (no confundir con menu_items de la caja).
        Schema::create('landing_productos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 60);
            $table->string('descripcion', 200);
            $table->string('imagen')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_productos');
    }
};
