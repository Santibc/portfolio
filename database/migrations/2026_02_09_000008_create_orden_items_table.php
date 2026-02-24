<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orden_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('catalogo_item_id')->nullable();
            $table->string('codigo', 50)->nullable();
            $table->text('descripcion');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('porcentaje_iva', 5, 2)->default(19.00);
            $table->string('categoria', 50);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('monto_iva', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();

            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('catalogo_item_id')->references('id')->on('catalogo_items')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_items');
    }
};
