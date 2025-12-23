<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->foreignId('obra_id')->nullable()->constrained('obras')->nullOnDelete();
            $table->date('fecha');

            // Check-in
            $table->time('hora_entrada')->nullable();
            $table->decimal('latitud_entrada', 10, 8)->nullable();
            $table->decimal('longitud_entrada', 11, 8)->nullable();

            // Check-out
            $table->time('hora_salida')->nullable();
            $table->decimal('latitud_salida', 10, 8)->nullable();
            $table->decimal('longitud_salida', 11, 8)->nullable();

            // Calculado
            $table->decimal('horas_trabajadas', 5, 2)->nullable();
            $table->decimal('horas_extra', 5, 2)->default(0);

            // Validación
            $table->boolean('validado')->default(false);
            $table->foreignId('validado_por')->nullable()->constrained('users');
            $table->datetime('fecha_validacion')->nullable();

            // Correcciones
            $table->boolean('corregido')->default(false);
            $table->foreignId('corregido_por')->nullable()->constrained('users');
            $table->text('motivo_correccion')->nullable();

            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['trabajador_id', 'fecha'], 'unique_fichaje_dia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichajes');
    }
};
