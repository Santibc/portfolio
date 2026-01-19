<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_venta_pdv', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_pdv_id')->constrained('ventas_pdv')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items_venta_pdv');
    }
};
