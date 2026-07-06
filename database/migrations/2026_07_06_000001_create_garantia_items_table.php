<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Productos reclamados en una garantía. Una garantía puede tener 0..N productos
     * (el producto es opcional al registrar) y cada uno con su cantidad.
     */
    public function up(): void
    {
        Schema::create('garantia_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garantia_id')->constrained('garantias')->cascadeOnDelete();
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->unsignedBigInteger('variante_producto_id')->nullable();
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('productos')->nullOnDelete();
            $table->foreign('variante_producto_id')->references('id')->on('variantes_productos')->nullOnDelete();
            $table->index('garantia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garantia_items');
    }
};
