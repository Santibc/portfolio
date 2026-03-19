<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sesiones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas');
            $table->foreignId('usuario_id')->constrained('users');
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->decimal('monto_apertura', 12, 2);
            $table->decimal('total_ventas_efectivo', 12, 2)->default(0);
            $table->decimal('total_ventas_transferencia', 12, 2)->default(0);
            $table->decimal('total_ventas', 12, 2)->default(0);
            $table->unsignedInteger('cantidad_ventas')->default(0);
            $table->decimal('total_vales', 12, 2)->default(0);
            $table->decimal('total_anulaciones', 12, 2)->default(0);
            $table->decimal('monto_esperado_efectivo', 12, 2)->nullable();
            $table->decimal('monto_contado', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->text('observaciones_cierre')->nullable();
            $table->timestamp('abierta_en');
            $table->timestamp('cerrada_en')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sesiones_caja');
    }
};
