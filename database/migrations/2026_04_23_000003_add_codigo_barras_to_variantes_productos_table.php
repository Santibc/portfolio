<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->string('codigo_barras', 50)->nullable()->unique()->after('sku');
        });
    }

    public function down()
    {
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->dropUnique(['codigo_barras']);
            $table->dropColumn('codigo_barras');
        });
    }
};
