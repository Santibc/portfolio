<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->decimal('total_devoluciones', 12, 2)->default(0)->after('total');
        });
    }

    public function down()
    {
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->dropColumn('total_devoluciones');
        });
    }
};
