<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('items_devolucion_parcial_pdv', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_parcial_pdv_id')->constrained('devoluciones_parciales_pdv')->cascadeOnDelete();
            $table->foreignId('item_venta_pdv_id')->constrained('items_venta_pdv');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos')->nullOnDelete();
            $table->unsignedInteger('cantidad_devuelta');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('descuento_porcentaje', 10, 2)->default(0);
            $table->decimal('descuento_valor', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items_devolucion_parcial_pdv');
    }
};
