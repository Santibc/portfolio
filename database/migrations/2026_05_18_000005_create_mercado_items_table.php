<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercado_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mercado_id')
                ->constrained('mercados')
                ->cascadeOnDelete();
            $table->foreignId('lista_mercado_item_id')
                ->nullable()
                ->constrained('lista_mercado_items')
                ->nullOnDelete();
            $table->foreignId('producto_mercado_id')
                ->constrained('productos_mercado')
                ->restrictOnDelete();
            $table->foreignId('tipo_producto_mercado_id')
                ->constrained('tipos_producto_mercado')
                ->restrictOnDelete();
            $table->unsignedInteger('cantidad_sugerida');
            $table->string('estado', 20)->default('pendiente')->index();
            $table->foreignId('registro_mercado_id')
                ->nullable()
                ->constrained('registros_mercado')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['mercado_id', 'lista_mercado_item_id']);
            $table->index(['mercado_id', 'tipo_producto_mercado_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mercado_items');
    }
};
