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
        Schema::table('traslados_stock', function (Blueprint $table) {
            $table->text('observacion_recepcion')->nullable()->after('notas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('traslados_stock', function (Blueprint $table) {
            $table->dropColumn('observacion_recepcion');
        });
    }
};
