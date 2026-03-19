<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items_venta_pdv', function (Blueprint $table) {
            $table->decimal('precio_original', 10, 2)->nullable()->after('precio_unitario');
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->after('precio_original');
            $table->decimal('descuento_valor', 10, 2)->default(0)->after('descuento_porcentaje');
        });
    }

    public function down()
    {
        Schema::table('items_venta_pdv', function (Blueprint $table) {
            $table->dropColumn(['precio_original', 'descuento_porcentaje', 'descuento_valor']);
        });
    }
};
