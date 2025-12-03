<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inversiones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_inversion', 50)->unique();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('compra_cross_fund_id')->nullable();
            $table->decimal('monto_invertido', 15, 2);
            $table->decimal('valor_actual', 15, 2);
            $table->decimal('ganancia_acumulada', 15, 2)->default(0);
            $table->decimal('dividendos_acumulados', 15, 2)->default(0);
            $table->date('fecha_inversion');
            $table->date('fecha_vencimiento');
            $table->date('fecha_retiro')->nullable();
            $table->enum('estado', ['pendiente_pago', 'activa', 'en_trading', 'vendida', 'vencida', 'retirada_anticipada', 'cancelada'])->default('pendiente_pago');
            $table->boolean('disponible_trading')->default(false);
            $table->decimal('precio_venta_sugerido', 15, 2)->nullable();
            $table->unsignedBigInteger('contrato_id')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('restrict');
            $table->foreign('compra_cross_fund_id')->references('id')->on('compras_cross_fund')->onDelete('set null');
            $table->foreign('contrato_id')->references('id')->on('plantillas_contrato')->onDelete('set null');

            $table->index('codigo_inversion');
            $table->index('usuario_id');
            $table->index('proyecto_id');
            $table->index('estado');
            $table->index('disponible_trading');
            $table->index('fecha_vencimiento');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inversiones');
    }
};
