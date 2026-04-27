<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->string('siigo_product_code', 50)->nullable()->after('codigo_barras');
            $table->index('siigo_product_code', 'variantes_productos_siigo_product_code_index');
        });
    }

    public function down()
    {
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->dropIndex('variantes_productos_siigo_product_code_index');
            $table->dropColumn('siigo_product_code');
        });
    }
};
