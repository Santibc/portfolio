<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('imagenes_productos', function (Blueprint $table) {
            $table->unsignedBigInteger('variante_producto_id')
                  ->nullable()
                  ->after('producto_id');

            $table->foreign('variante_producto_id')
                  ->references('id')
                  ->on('variantes_productos')
                  ->onDelete('cascade');

            $table->index(['producto_id', 'variante_producto_id'], 'img_prod_variante_idx');
        });
    }

    public function down()
    {
        Schema::table('imagenes_productos', function (Blueprint $table) {
            $table->dropForeign(['variante_producto_id']);
            $table->dropIndex('img_prod_variante_idx');
            $table->dropColumn('variante_producto_id');
        });
    }
};
