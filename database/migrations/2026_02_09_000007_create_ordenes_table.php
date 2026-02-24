<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 20)->unique()->nullable();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('creado_por');
            $table->string('estado_trabajo', 50)->default('borrador');
            $table->string('estado_entrega', 50)->nullable();
            $table->string('estado_pago', 50)->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->time('hora_entrega')->nullable();
            $table->string('ruta_firma_cliente', 500)->nullable();
            $table->text('notas')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('monto_iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_pagado', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            $table->unsignedBigInteger('clonada_de_id')->nullable();
            $table->unsignedBigInteger('bloqueada_por')->nullable();
            $table->timestamp('bloqueada_en')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('creado_por')->references('id')->on('users');
            $table->foreign('clonada_de_id')->references('id')->on('ordenes')->onDelete('set null');
            $table->foreign('bloqueada_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ordenes');
    }
};
