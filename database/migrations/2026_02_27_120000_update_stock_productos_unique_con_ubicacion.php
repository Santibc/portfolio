<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Actualiza el constraint unique de stock_productos para incluir ubicacion_id,
     * permitiendo tener stock del mismo producto/variante en múltiples ubicaciones.
     */
    public function up()
    {
        Schema::table('stock_productos', function (Blueprint $table) {
            // Eliminar el constraint unique antiguo (producto_id, variante_producto_id)
            $table->dropUnique(['producto_id', 'variante_producto_id']);

            // Crear nuevo constraint unique incluyendo ubicacion_id
            $table->unique(['producto_id', 'variante_producto_id', 'ubicacion_id'], 'stock_prod_var_ubic_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('stock_productos', function (Blueprint $table) {
            $table->dropUnique('stock_prod_var_ubic_unique');
            $table->unique(['producto_id', 'variante_producto_id']);
        });
    }
};
