<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Override de lista de precios por caja. Si está seteada (caja de feria), el POS
     * de esa caja cobra con esa lista; si es NULL, usa la lista por defecto del PDV.
     */
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->unsignedBigInteger('lista_precio_id')->nullable()->after('ubicacion_id');
            $table->foreign('lista_precio_id')->references('id')->on('listas_precios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropForeign(['lista_precio_id']);
            $table->dropColumn('lista_precio_id');
        });
    }
};
