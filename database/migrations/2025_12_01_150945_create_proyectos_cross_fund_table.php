<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyectos_cross_fund', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paquete_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->decimal('porcentaje_asignacion', 5, 2);
            $table->decimal('monto_asignado', 15, 2);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->foreign('paquete_id')->references('id')->on('paquetes_cross_fund')->onDelete('cascade');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('restrict');

            $table->unique(['paquete_id', 'proyecto_id']);
            $table->index('paquete_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyectos_cross_fund');
    }
};
