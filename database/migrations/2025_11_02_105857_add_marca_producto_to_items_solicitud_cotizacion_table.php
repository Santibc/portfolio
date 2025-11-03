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
        Schema::table('items_solicitud_cotizacion', function (Blueprint $table) {
            $table->string('marca_producto', 255)->nullable()->after('nombre_producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items_solicitud_cotizacion', function (Blueprint $table) {
            $table->dropColumn('marca_producto');
        });
    }
};
