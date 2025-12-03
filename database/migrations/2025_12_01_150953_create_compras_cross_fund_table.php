<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('compras_cross_fund', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_compra', 50)->unique();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('paquete_id');
            $table->decimal('monto_total', 15, 2);
            $table->decimal('roi_ponderado', 5, 2);
            $table->integer('duracion_promedio');
            $table->date('fecha_compra');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['activa', 'vencida', 'cancelada'])->default('activa');
            $table->unsignedBigInteger('contrato_id')->nullable();
            $table->boolean('contrato_aceptado')->default(false);
            $table->timestamp('contrato_aceptado_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('paquete_id')->references('id')->on('paquetes_cross_fund')->onDelete('restrict');
            $table->foreign('contrato_id')->references('id')->on('plantillas_contrato')->onDelete('set null');

            $table->index('codigo_compra');
            $table->index('usuario_id');
            $table->index('paquete_id');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('compras_cross_fund');
    }
};
