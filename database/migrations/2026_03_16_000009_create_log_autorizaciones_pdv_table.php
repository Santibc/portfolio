<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('log_autorizaciones_pdv', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['descuento', 'precio', 'anulacion', 'vale_anulacion', 'descuento_global']);
            $table->string('referencia_tipo');
            $table->unsignedBigInteger('referencia_id');
            $table->foreignId('usuario_solicitante_id')->constrained('users');
            $table->foreignId('usuario_autorizador_id')->constrained('users');
            $table->json('detalle')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_autorizaciones_pdv');
    }
};
