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
        Schema::create('reglas_capacidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->date('fecha'); // Fecha específica para regla especial
            $table->integer('capacidad_total_dia')->default(50); // Pedidos máximos por día
            $table->integer('capacidad_por_hora')->default(5); // Pedidos máximos por hora
            $table->text('notas')->nullable(); // Ej: "Día de San Valentín - alta demanda"
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['empresa_id', 'fecha', 'activo']);
            $table->unique(['empresa_id', 'fecha']); // Una sola regla por fecha
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reglas_capacidad');
    }
};
