<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historial_avances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_pieza_id');
            $table->unsignedBigInteger('operario_id');
            $table->decimal('porcentaje_desde', 5, 2);
            $table->decimal('porcentaje_hasta', 5, 2);
            $table->decimal('contribucion', 5, 2);
            $table->text('notas')->nullable();
            $table->timestamp('asignado_en');
            $table->timestamp('completado_en')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('orden_pieza_id')->references('id')->on('orden_piezas')->onDelete('cascade');
            $table->foreign('operario_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_avances');
    }
};
