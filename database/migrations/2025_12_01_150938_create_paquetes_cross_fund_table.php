<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paquetes_cross_fund', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion');
            $table->decimal('monto_paquete', 15, 2);
            $table->decimal('roi_ponderado', 5, 2);
            $table->integer('duracion_promedio_meses');
            $table->enum('estado', ['borrador', 'activo', 'agotado', 'cerrado'])->default('borrador');
            $table->integer('cantidad_disponible')->default(0);
            $table->integer('cantidad_vendida')->default(0);
            $table->boolean('destacado')->default(false);
            $table->date('fecha_inicio_venta');
            $table->date('fecha_fin_venta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('codigo');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paquetes_cross_fund');
    }
};
