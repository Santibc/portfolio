<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reglas_penalizacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_id');
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->enum('tipo_penalizacion', ['porcentaje_fijo', 'porcentaje_variable', 'dias_retencion']);
            $table->decimal('valor', 5, 2);
            $table->integer('aplica_desde_mes');
            $table->integer('aplica_hasta_mes');
            $table->boolean('pierde_capital')->default(false);
            $table->boolean('pierde_dividendos')->default(true);
            $table->boolean('permite_venta_posicion')->default(true);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->foreign('categoria_id')->references('id')->on('categorias_proyecto')->onDelete('restrict');
            $table->index('categoria_id');
            $table->index('activo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reglas_penalizacion');
    }
};
