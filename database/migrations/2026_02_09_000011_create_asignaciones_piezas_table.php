<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asignaciones_piezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_pieza_id');
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('asignado_desde_id')->nullable();
            $table->unsignedBigInteger('asignado_a_id');
            $table->unsignedBigInteger('asignado_por_id');
            $table->string('tipo_asignacion', 50);
            $table->decimal('porcentaje_al_asignar', 5, 2);
            $table->text('notas')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamp('created_at')->nullable();

            $table->foreign('orden_pieza_id')->references('id')->on('orden_piezas')->onDelete('cascade');
            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('asignado_desde_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('asignado_a_id')->references('id')->on('users');
            $table->foreign('asignado_por_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asignaciones_piezas');
    }
};
