<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obra_hitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->integer('porcentaje_obra')->nullable()->comment('Ej: 30 = 30% completado');
            $table->date('fecha_prevista')->nullable();
            $table->date('fecha_completado')->nullable();
            $table->decimal('importe_cobro', 12, 2)->nullable()->comment('Cobro parcial asociado al hito');
            $table->boolean('completado')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obra_hitos');
    }
};
