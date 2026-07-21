<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos')->cascadeOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('precio_total', 14, 2);
            $table->string('referencia_producto')->nullable();
            $table->string('nombre_producto')->nullable();
            $table->string('info_variante')->nullable();
            $table->timestamps();

            $table->index(['venta_id']);
            $table->index(['producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items_venta');
    }
};
