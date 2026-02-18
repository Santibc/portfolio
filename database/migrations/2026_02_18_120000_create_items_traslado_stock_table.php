<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('items_traslado_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_stock_id')->constrained('traslados_stock')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items_traslado_stock');
    }
};
