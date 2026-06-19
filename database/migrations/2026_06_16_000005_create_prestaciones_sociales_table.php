<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestaciones_sociales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->restrictOnDelete();
            $table->foreignId('metodo_pago_id')
                ->nullable()
                ->constrained('metodos_pago')
                ->nullOnDelete();
            $table->string('tipo', 20);                 // TipoPrestacion
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedSmallInteger('dias');
            $table->unsignedInteger('base');
            $table->unsignedInteger('valor');
            $table->unsignedInteger('intereses')->default(0);
            $table->string('fondo', 80)->nullable();
            $table->string('estado', 20)->default('pendiente');  // EstadoPrestacion
            $table->date('fecha_pago')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['empleado_id', 'tipo']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestaciones_sociales');
    }
};
