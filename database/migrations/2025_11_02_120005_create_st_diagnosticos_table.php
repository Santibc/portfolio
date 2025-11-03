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
        Schema::create('st_diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('st_orden_servicio_id')->constrained('st_ordenes_servicio')->onDelete('cascade');
            $table->foreignId('st_tecnico_id')->constrained('st_tecnicos')->onDelete('restrict');
            $table->text('diagnostico_tecnico');
            $table->text('fallas_encontradas');
            $table->text('reparaciones_realizadas')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->boolean('requiere_repuestos')->default(false);
            $table->text('repuestos_necesarios')->nullable();
            $table->decimal('tiempo_estimado_horas', 8, 2)->nullable();
            $table->decimal('costo_estimado', 12, 2)->nullable();
            $table->boolean('aprobado_por_cliente')->nullable();
            $table->date('fecha_diagnostico');
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
        Schema::dropIfExists('st_diagnosticos');
    }
};
