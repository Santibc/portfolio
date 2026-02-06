<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->boolean('stock_descontado')->default(false)->after('reserva_liberada_en');
            $table->timestamp('stock_descontado_en')->nullable()->after('stock_descontado');
            $table->unsignedBigInteger('stock_descontado_por')->nullable()->after('stock_descontado_en');

            $table->foreign('stock_descontado_por')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->dropForeign(['stock_descontado_por']);
            $table->dropColumn(['stock_descontado', 'stock_descontado_en', 'stock_descontado_por']);
        });
    }
};
