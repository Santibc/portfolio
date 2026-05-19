<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete();
            $table->foreignId('metodo_pago_id')
                ->constrained('metodos_pago')
                ->restrictOnDelete();
            $table->unsignedInteger('monto');
            $table->string('referencia', 100)->nullable();
            $table->timestamps();

            $table->index(['metodo_pago_id', 'created_at']);
            $table->index('venta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_pagos');
    }
};
