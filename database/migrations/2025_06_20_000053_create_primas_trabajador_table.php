<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('primas_trabajador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->foreignId('parte_diario_id')->nullable()->constrained('partes_diarios')->nullOnDelete();
            $table->foreignId('prima_configuracion_id')->constrained('prima_configuraciones');

            $table->date('fecha');

            // Cálculo
            $table->decimal('produccion_equipo', 12, 2)->comment('Total producido por el equipo');
            $table->integer('trabajadores_equipo');
            $table->decimal('minimo_requerido', 12, 2)->comment('minimo_por_trabajador * trabajadores_equipo');
            $table->decimal('excedente', 12, 2)->comment('produccion_equipo - minimo_requerido');
            $table->integer('tramos_conseguidos')->comment('excedente / tramo_prima');
            $table->decimal('importe_prima', 10, 2)->comment('tramos * importe_por_trabajador');

            $table->boolean('pagada')->default(false);
            $table->date('fecha_pago')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('primas_trabajador');
    }
};
