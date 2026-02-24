<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago', 50);
            $table->string('referencia_pago', 255)->nullable();
            $table->unsignedBigInteger('registrado_por');
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->boolean('aprobado')->default(false);
            $table->timestamps();

            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('registrado_por')->references('id')->on('users');
            $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
