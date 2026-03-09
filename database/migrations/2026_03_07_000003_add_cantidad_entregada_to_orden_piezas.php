<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orden_piezas', function (Blueprint $table) {
            $table->integer('cantidad_entregada')->default(0)->after('cantidad');
        });
    }

    public function down()
    {
        Schema::table('orden_piezas', function (Blueprint $table) {
            $table->dropColumn('cantidad_entregada');
        });
    }
};
