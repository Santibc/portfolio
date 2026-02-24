<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tabla_precios_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_servicio', 100);
            $table->string('etiqueta_servicio', 255);
            $table->string('clave_calibre', 20);
            $table->decimal('calibre_mm', 5, 2);
            $table->integer('largo_rango_min');
            $table->integer('largo_rango_max')->nullable();
            $table->integer('cantidad_rango_min');
            $table->integer('cantidad_rango_max')->nullable();
            $table->decimal('precio', 12, 2);
            $table->decimal('precio_minimo', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tabla_precios_servicios');
    }
};
