<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete();
            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->restrictOnDelete();
            $table->string('nombre_snapshot', 200);
            $table->unsignedInteger('precio_unitario');
            $table->unsignedSmallInteger('cantidad');
            $table->unsignedInteger('subtotal');
            $table->timestamps();

            $table->index('venta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_items');
    }
};
