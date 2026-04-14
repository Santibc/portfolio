<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orden_items', function (Blueprint $table) {
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->after('porcentaje_iva');
            $table->decimal('descuento_monto', 12, 2)->default(0)->after('descuento_porcentaje');
        });
    }

    public function down()
    {
        Schema::table('orden_items', function (Blueprint $table) {
            $table->dropColumn(['descuento_porcentaje', 'descuento_monto']);
        });
    }
};
