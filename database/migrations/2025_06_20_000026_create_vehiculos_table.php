<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_tipo_id')->constrained('vehiculo_tipos');
            $table->string('matricula', 20)->unique();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_bastidor', 100)->nullable();

            // Fechas
            $table->date('fecha_matriculacion')->nullable();
            $table->date('fecha_compra')->nullable();

            // ITV
            $table->date('fecha_ultima_itv')->nullable();
            $table->date('fecha_proxima_itv')->nullable();

            // Seguro
            $table->string('compania_seguro', 150)->nullable();
            $table->string('numero_poliza', 100)->nullable();
            $table->date('fecha_vencimiento_seguro')->nullable();

            // Económico
            $table->decimal('coste_adquisicion', 12, 2)->nullable();
            $table->decimal('coste_dia', 8, 2)->nullable();

            // Estado
            $table->enum('estado', ['operativo', 'en_taller', 'baja'])->default('operativo');
            $table->integer('kilometraje_actual')->nullable();

            // Asignación
            $table->foreignId('conductor_habitual_id')->nullable()->constrained('trabajadores')->nullOnDelete();

            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
