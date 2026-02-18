<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('traslados_stock', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['variante_producto_id']);
            $table->dropColumn(['producto_id', 'variante_producto_id', 'cantidad']);
        });
    }

    public function down()
    {
        Schema::table('traslados_stock', function (Blueprint $table) {
            $table->foreignId('producto_id')->nullable()->constrained('productos');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos');
            $table->integer('cantidad')->nullable();
        });
    }
};
