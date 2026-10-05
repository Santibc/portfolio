<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_item_componente_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_componente_id')
                ->constrained('menu_item_componentes')
                ->cascadeOnDelete();
            $table->foreignId('producto_mercado_id')
                ->constrained('productos_mercado')
                ->restrictOnDelete();
            $table->timestamps();

            $table->unique(['menu_item_componente_id', 'producto_mercado_id'], 'componente_producto_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_componente_opciones');
    }
};
