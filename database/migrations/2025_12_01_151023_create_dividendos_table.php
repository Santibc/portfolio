<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dividendos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_dividendo', 50)->unique();
            $table->unsignedBigInteger('inversion_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('usuario_id');
            $table->integer('numero_periodo');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_programada');
            $table->date('fecha_pagada')->nullable();
            $table->enum('estado', ['programado', 'pagado', 'atrasado', 'cancelado'])->default('programado');
            $table->unsignedBigInteger('pagado_por')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('inversion_id')->references('id')->on('inversiones')->onDelete('restrict');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('pagado_por')->references('id')->on('users')->onDelete('set null');

            $table->index('codigo_dividendo');
            $table->index('inversion_id');
            $table->index('usuario_id');
            $table->index('estado');
            $table->index('fecha_programada');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dividendos');
    }
};
