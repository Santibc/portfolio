<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria')->nullable(); // Lentes, Placas, Fuentes, Cables, Sensores, etc.
            $table->string('marca')->nullable();
            $table->string('modelo_compatible')->nullable();
            $table->decimal('precio_costo', 12, 2)->nullable();
            $table->decimal('precio_venta', 12, 2)->nullable();
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->string('ubicacion_bodega')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_repuestos');
    }
};
