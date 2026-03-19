<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('log_traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_stock_id')->constrained('traslados_stock')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('accion', 50); // creacion, edicion, envio, recepcion, cancelacion
            $table->json('detalle')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_traslados');
    }
};
