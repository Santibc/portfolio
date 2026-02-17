<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->boolean('marcada_inventario')->default(false)->after('stock_descontado_por');
        });
    }

    public function down()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->dropColumn('marcada_inventario');
        });
    }
};
