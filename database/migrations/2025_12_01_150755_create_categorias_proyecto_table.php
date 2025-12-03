<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categorias_proyecto', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('duracion_minima_meses')->nullable();
            $table->integer('duracion_maxima_meses')->nullable();
            $table->decimal('roi_minimo', 5, 2)->nullable();
            $table->decimal('roi_maximo', 5, 2)->nullable();
            $table->decimal('inversion_minima', 15, 2)->default(0);
            $table->decimal('inversion_maxima', 15, 2)->nullable();
            $table->boolean('permite_retiro_anticipado')->default(false);
            $table->boolean('permite_trading')->default(false);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index('codigo');
            $table->index('activo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categorias_proyecto');
    }
};
