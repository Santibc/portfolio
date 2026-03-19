<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vales_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_caja_id')->constrained('sesiones_caja');
            $table->foreignId('caja_id')->constrained('cajas');
            $table->text('descripcion');
            $table->decimal('monto', 12, 2);
            $table->enum('estado', ['pendiente', 'redimido', 'anulado'])->default('pendiente');
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('anulado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo_anulacion')->nullable();
            $table->timestamp('anulado_en')->nullable();
            $table->timestamp('redimido_en')->nullable();
            $table->foreignId('redimido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vales_caja');
    }
};
