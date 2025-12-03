<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->string('banco', 100);
            $table->enum('tipo_cuenta', ['ahorros', 'corriente', 'nequi', 'daviplata']);
            $table->text('numero_cuenta');
            $table->string('titular', 200);
            $table->string('documento_titular', 50);
            $table->boolean('es_principal')->default(false);
            $table->boolean('verificada')->default(false);
            $table->date('fecha_verificacion')->nullable();
            $table->unsignedBigInteger('verificada_por')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verificada_por')->references('id')->on('users')->onDelete('set null');

            $table->index('usuario_id');
            $table->index('es_principal');
            $table->index('verificada');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuentas_bancarias');
    }
};
