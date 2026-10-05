<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kardex: el stock de un producto es SUM(cantidad) de sus movimientos (entradas +, salidas −).
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_mercado_id')
                ->constrained('productos_mercado')
                ->restrictOnDelete();
            $table->string('tipo', 20);
            $table->decimal('cantidad', 12, 2);
            $table->foreignId('registro_mercado_id')
                ->nullable()
                ->unique()
                ->constrained('registros_mercado')
                ->cascadeOnDelete();
            $table->foreignId('venta_item_id')
                ->nullable()
                ->constrained('venta_items')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('motivo', 255)->nullable();
            $table->timestamps();

            $table->index(['producto_mercado_id', 'created_at']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
