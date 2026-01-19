<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Agregar 'traslado' y 'novedad' al enum de origen
        DB::statement("ALTER TABLE movimientos_stock MODIFY COLUMN origen ENUM('compra','venta','devolucion','ajuste_inventario','cotizacion','traslado','novedad','otro') NOT NULL DEFAULT 'otro'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE movimientos_stock MODIFY COLUMN origen ENUM('compra','venta','devolucion','ajuste_inventario','cotizacion','otro') NOT NULL DEFAULT 'otro'");
    }
};
