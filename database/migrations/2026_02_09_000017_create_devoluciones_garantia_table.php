<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('devoluciones_garantia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('orden_pieza_id');
            $table->integer('cantidad_devuelta');
            $table->text('motivo');
            $table->boolean('cobrable')->default(false);
            $table->decimal('monto_cobro', 12, 2)->nullable();
            $table->string('estado', 50)->default('abierta');
            $table->unsignedBigInteger('operario_asignado_id')->nullable();
            $table->unsignedBigInteger('registrado_por');
            $table->timestamp('completada_en')->nullable();
            $table->timestamp('reentregada_en')->nullable();
            $table->timestamps();

            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('orden_pieza_id')->references('id')->on('orden_piezas')->onDelete('cascade');
            $table->foreign('operario_asignado_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('registrado_por')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devoluciones_garantia');
    }
};
