<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código interno');
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('obra_tipo_id')->nullable()->constrained('obra_tipos');

            // Ubicación
            $table->text('direccion')->nullable();
            $table->string('localidad', 150)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->decimal('coordenadas_lat', 10, 8)->nullable();
            $table->decimal('coordenadas_lng', 11, 8)->nullable();

            // Datos ADIF (específicos para obras ferroviarias)
            $table->string('linea', 100)->nullable()->comment('Ej: L220 E1');
            $table->string('trayecto', 255)->nullable()->comment('Ej: Calaf - Manresa');
            $table->string('pk_inicio', 20)->nullable();
            $table->string('pk_fin', 20)->nullable();
            $table->string('gerencia_jefatura', 50)->nullable()->comment('BCN, ZGZ, etc.');
            $table->string('distrito', 100)->nullable();

            // Fechas
            $table->date('fecha_inicio_prevista')->nullable();
            $table->date('fecha_fin_prevista')->nullable();
            $table->date('fecha_inicio_real')->nullable();
            $table->date('fecha_fin_real')->nullable();

            // Economía
            $table->decimal('presupuesto', 14, 2)->nullable();
            $table->decimal('coste_estimado', 14, 2)->nullable();
            $table->decimal('margen_previsto', 14, 2)->nullable();

            // Estado y riesgo
            $table->enum('estado', ['presentada', 'aprobada', 'en_curso', 'pausada', 'finalizada', 'cancelada'])->default('presentada');
            $table->enum('riesgo_operativo', ['bajo', 'medio', 'alto'])->default('bajo');

            // Penalizaciones
            $table->boolean('tiene_penalizaciones')->default(false);
            $table->decimal('importe_penalizacion_prevista', 12, 2)->nullable();

            // Contrato asociado
            $table->foreignId('contrato_id')->nullable()->constrained('contratos')->nullOnDelete();

            // Centro de coste
            $table->string('centro_coste', 50)->nullable();

            // Responsables
            $table->foreignId('encargado_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Añadir FK a maquinaria para obra_asignada_id
        Schema::table('maquinaria', function (Blueprint $table) {
            $table->foreign('obra_asignada_id')->references('id')->on('obras')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maquinaria', function (Blueprint $table) {
            $table->dropForeign(['obra_asignada_id']);
        });

        Schema::dropIfExists('obras');
    }
};
