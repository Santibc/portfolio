<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('billeteras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->unique();
            $table->decimal('saldo_disponible', 15, 2)->default(0);
            $table->decimal('saldo_bloqueado', 15, 2)->default(0);
            $table->decimal('saldo_invertido', 15, 2)->default(0);
            $table->decimal('retornos_acumulados', 15, 2)->default(0);
            $table->decimal('dividendos_pendientes', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('usuario_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('billeteras');
    }
};
