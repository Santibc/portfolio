<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transacciones_inversion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_transaccion', 50)->unique();
            $table->unsignedBigInteger('inversion_id');
            $table->unsignedBigInteger('vendedor_id');
            $table->unsignedBigInteger('comprador_id');
            $table->decimal('monto_venta', 15, 2);
            $table->decimal('valor_libro', 15, 2);
            $table->decimal('ganancia_perdida', 15, 2);
            $table->decimal('comision_plataforma', 15, 2);
            $table->date('fecha_transaccion');
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('inversion_id')->references('id')->on('inversiones')->onDelete('restrict');
            $table->foreign('vendedor_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('comprador_id')->references('id')->on('users')->onDelete('restrict');

            $table->index('inversion_id');
            $table->index('vendedor_id');
            $table->index('comprador_id');
            $table->index('fecha_transaccion');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacciones_inversion');
    }
};
