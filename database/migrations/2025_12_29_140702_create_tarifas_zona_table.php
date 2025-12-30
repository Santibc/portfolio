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
        Schema::create('tarifas_zona', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zona_cobertura_id')->constrained('zonas_cobertura')->onDelete('cascade');
            $table->string('nombre'); // Ej: "Tarifa estándar", "Tarifa express"
            $table->decimal('costo_base', 10, 2)->default(0); // Costo fijo
            $table->decimal('costo_por_km', 10, 2)->default(0)->nullable(); // Opcional
            $table->decimal('minimo_compra', 10, 2)->default(0); // Mínimo para aplicar
            $table->decimal('costo_si_no_alcanza_minimo', 10, 2)->nullable(); // Costo extra si no alcanza mínimo
            $table->boolean('envio_gratis_desde')->default(false);
            $table->decimal('monto_envio_gratis', 10, 2)->nullable(); // Si compra supera este monto, envío gratis
            $table->integer('tiempo_entrega_horas')->default(24); // Tiempo estimado de entrega en horas
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['zona_cobertura_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarifas_zona');
    }
};
