<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('codigo_barras_logs', function (Blueprint $table) {
            $table->foreignId('variante_producto_id')
                  ->nullable()
                  ->after('producto_id')
                  ->constrained('variantes_productos')
                  ->onDelete('cascade');

            $table->index(['variante_producto_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::table('codigo_barras_logs', function (Blueprint $table) {
            $table->dropIndex(['variante_producto_id', 'created_at']);
            $table->dropForeign(['variante_producto_id']);
            $table->dropColumn('variante_producto_id');
        });
    }
};
