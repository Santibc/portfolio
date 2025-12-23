<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuadrilla_trabajadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuadrilla_id')->constrained('cuadrillas')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->date('fecha_incorporacion');
            $table->date('fecha_salida')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['cuadrilla_id', 'trabajador_id', 'activo'], 'unique_cuadrilla_trabajador_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuadrilla_trabajadores');
    }
};
