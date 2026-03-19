<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->foreignId('cajero_asignado_id')->nullable()->constrained('users');
            $table->enum('estado', ['cerrada', 'abierta', 'en_cierre'])->default('cerrada');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cajas');
    }
};
