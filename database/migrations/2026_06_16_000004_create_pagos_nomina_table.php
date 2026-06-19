<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_nomina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_detalle_id')
                ->constrained('nomina_detalles')
                ->cascadeOnDelete();
            $table->foreignId('metodo_pago_id')
                ->constrained('metodos_pago')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->unsignedInteger('monto');
            $table->string('referencia', 100)->nullable();
            $table->date('fecha_pago');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nomina_detalle_id', 'fecha_pago']);
            $table->index('metodo_pago_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_nomina');
    }
};
