<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prima_configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->foreignId('obra_tipo_id')->nullable()->constrained('obra_tipos')->nullOnDelete()->comment('NULL = aplica a todos los tipos');

            // Mínimo requerido
            $table->enum('unidad_medida', ['m2', 'unidades', 'hectareas']);
            $table->decimal('minimo_por_trabajador', 10, 2)->comment('Ej: 2500 m²/trabajador');

            // Prima
            $table->decimal('tramo_prima', 10, 2)->comment('Cada X unidades extra = prima');
            $table->decimal('importe_prima_por_trabajador', 8, 2)->comment('€ por trabajador por tramo');

            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prima_configuraciones');
    }
};
