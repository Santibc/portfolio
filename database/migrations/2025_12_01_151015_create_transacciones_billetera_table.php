<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transacciones_billetera', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_transaccion', 50)->unique();
            $table->unsignedBigInteger('billetera_id');
            $table->unsignedBigInteger('usuario_id');
            $table->enum('tipo', ['deposito', 'retiro', 'inversion', 'dividendo', 'retorno_capital', 'venta_trading', 'compra_trading', 'comision', 'reversa', 'ajuste']);
            $table->decimal('monto', 15, 2);
            $table->enum('naturaleza', ['credito', 'debito']);
            $table->decimal('saldo_anterior', 15, 2);
            $table->decimal('saldo_posterior', 15, 2);
            $table->text('descripcion')->nullable();
            $table->string('referencia_externa', 100)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('referencia_type', 100)->nullable();
            $table->unsignedBigInteger('procesado_por')->nullable();
            $table->timestamp('fecha_transaccion');
            $table->timestamps();

            $table->foreign('billetera_id')->references('id')->on('billeteras')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('procesado_por')->references('id')->on('users')->onDelete('set null');

            $table->index('codigo_transaccion');
            $table->index('billetera_id');
            $table->index('usuario_id');
            $table->index('tipo');
            $table->index('fecha_transaccion');
            $table->index(['referencia_id', 'referencia_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacciones_billetera');
    }
};
