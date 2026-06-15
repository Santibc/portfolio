<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomina_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_id')
                ->constrained('nominas')
                ->cascadeOnDelete();
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->restrictOnDelete();
            $table->string('empleado_nombre', 120);     // snapshot

            // Inputs snapshot
            $table->unsignedSmallInteger('dias');
            $table->unsignedInteger('salario_base');
            $table->unsignedInteger('auxilio');
            $table->unsignedInteger('bono')->default(0);
            $table->unsignedSmallInteger('porcentaje_salud');
            $table->unsignedSmallInteger('porcentaje_pension');

            // Calculados snapshot (congelados al liquidar)
            $table->unsignedInteger('basico');
            $table->unsignedInteger('salud');
            $table->unsignedInteger('pension');
            $table->unsignedInteger('total_devengado');
            $table->unsignedInteger('total_deducido');
            $table->unsignedInteger('neto');

            $table->unsignedInteger('ahorro')->default(0);
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['nomina_id', 'empleado_id']);
            $table->index('empleado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomina_detalles');
    }
};
