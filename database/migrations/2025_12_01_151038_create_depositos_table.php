<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('depositos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_deposito', 50)->unique();
            $table->unsignedBigInteger('usuario_id');
            $table->decimal('monto', 15, 2);
            $table->enum('metodo_pago', ['transferencia_bancaria', 'pse', 'tarjeta_credito', 'nequi', 'daviplata', 'efectivo', 'otro']);
            $table->string('referencia_pago', 200)->nullable();
            $table->string('comprobante', 500)->nullable();
            $table->date('fecha_deposito');
            $table->enum('estado', ['pendiente', 'verificado', 'rechazado'])->default('pendiente');
            $table->unsignedBigInteger('verificado_por')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('verificado_por')->references('id')->on('users')->onDelete('set null');

            $table->index('codigo_deposito');
            $table->index('usuario_id');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('depositos');
    }
};
