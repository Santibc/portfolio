<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listas_mercado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('slug')->unique();
            $table->boolean('activa')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listas_mercado');
    }
};
