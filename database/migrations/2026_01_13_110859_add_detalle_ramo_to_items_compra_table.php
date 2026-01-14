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
        Schema::table('items_compra', function (Blueprint $table) {
            $table->json('detalle_ramo')->nullable()->after('info_variante');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items_compra', function (Blueprint $table) {
            $table->dropColumn('detalle_ramo');
        });
    }
};
