<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos_fijos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concepto_gasto_fijo_id')
                ->constrained('conceptos_gasto_fijo')
                ->restrictOnDelete();
            $table->foreignId('metodo_pago_id')
                ->constrained('metodos_pago')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->unsignedInteger('valor');
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fecha');
            $table->index('metodo_pago_id');
            $table->index('concepto_gasto_fijo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos_fijos');
    }
};
