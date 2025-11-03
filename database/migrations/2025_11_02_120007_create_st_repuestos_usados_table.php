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
        Schema::create('st_repuestos_usados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('st_orden_servicio_id')->constrained('st_ordenes_servicio')->onDelete('cascade');
            $table->foreignId('st_repuesto_id')->constrained('st_repuestos')->onDelete('restrict');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
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
        Schema::dropIfExists('st_repuestos_usados');
    }
};
