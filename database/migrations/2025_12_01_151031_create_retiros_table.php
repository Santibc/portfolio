<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('retiros', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_retiro', 50)->unique();
            $table->unsignedBigInteger('usuario_id');
            $table->decimal('monto_solicitado', 15, 2);
            $table->decimal('monto_aprobado', 15, 2)->nullable();
            $table->decimal('comision', 15, 2)->default(0);
            $table->decimal('monto_neto', 15, 2)->nullable();
            $table->enum('metodo_pago', ['transferencia_bancaria', 'nequi', 'daviplata', 'otro']);
            $table->text('datos_pago');
            $table->date('fecha_solicitud');
            $table->date('fecha_aprobacion')->nullable();
            $table->date('fecha_rechazo')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->enum('estado', ['pendiente', 'en_revision', 'aprobado', 'rechazado', 'pagado', 'cancelado'])->default('pendiente');
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->unsignedBigInteger('pagado_por')->nullable();
            $table->text('notas_aprobacion')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->string('comprobante_pago', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pagado_por')->references('id')->on('users')->onDelete('set null');

            $table->index('codigo_retiro');
            $table->index('usuario_id');
            $table->index('estado');
            $table->index('fecha_solicitud');
        });
    }

    public function down()
    {
        Schema::dropIfExists('retiros');
    }
};
