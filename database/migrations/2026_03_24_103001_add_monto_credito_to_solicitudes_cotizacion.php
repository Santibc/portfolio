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
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->decimal('monto_credito', 12, 2)->nullable()->after('monto_pagado');
        });
    }

    public function down()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            $table->dropColumn('monto_credito');
        });
    }
};
