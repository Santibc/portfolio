<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquinaria_tipo_id')->constrained('maquinaria_tipos');
            $table->string('codigo_interno', 50)->unique()->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->string('numero_bastidor', 100)->nullable();

            // Económico
            $table->date('fecha_compra')->nullable();
            $table->decimal('coste_adquisicion', 12, 2)->nullable();
            $table->integer('vida_util_meses')->nullable();
            $table->decimal('amortizacion_dia', 8, 2)->nullable()->comment('€/día');
            $table->decimal('coste_hora', 8, 2)->nullable();

            // Estado
            $table->enum('estado', ['operativa', 'en_reparacion', 'baja'])->default('operativa');

            // Asignación actual (se llenará cuando exista la tabla obras)
            $table->unsignedBigInteger('obra_asignada_id')->nullable();
            $table->foreignId('trabajador_asignado_id')->nullable()->constrained('trabajadores')->nullOnDelete();

            // Documentación
            $table->boolean('tiene_marcado_ce')->default(true);
            $table->boolean('tiene_manual')->default(true);

            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria');
    }
};
