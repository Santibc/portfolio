<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_mercado_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_id')
                ->constrained('listas_mercado')
                ->cascadeOnDelete();
            $table->foreignId('producto_mercado_id')
                ->constrained('productos_mercado')
                ->restrictOnDelete();
            $table->unsignedInteger('cantidad_sugerida');
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['lista_id', 'producto_mercado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_mercado_items');
    }
};
