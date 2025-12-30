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
        Schema::create('horarios_entrega', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zona_cobertura_id')->constrained('zonas_cobertura')->onDelete('cascade');
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']);
            $table->time('hora_inicio'); // Ej: 08:00
            $table->time('hora_fin'); // Ej: 18:00
            $table->string('nombre')->nullable(); // Ej: "Mañana", "Tarde", "Noche"
            $table->integer('capacidad_pedidos')->default(10); // Cantidad máxima de pedidos en este horario
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['zona_cobertura_id', 'dia_semana', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horarios_entrega');
    }
};
