<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partes_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('jornada', ['diurna', 'nocturna'])->default('diurna');

            // Datos ADIF específicos
            $table->string('linea', 100)->nullable();
            $table->string('trayecto', 255)->nullable();
            $table->string('gerencia_jefatura', 50)->nullable();
            $table->string('distrito', 100)->nullable();

            // Brigada
            $table->string('brigada', 100)->nullable()->comment('MANZER, subcontrata, etc.');

            // Totales del día
            $table->decimal('desbroce_total_m2', 12, 2)->default(0);
            $table->decimal('desbroce_p5_m2', 12, 2)->default(0);
            $table->decimal('desbroce_p6_m2', 12, 2)->default(0);
            $table->decimal('limpieza_p8_m2', 12, 2)->default(0);
            $table->decimal('herbicida_p4_m2', 12, 2)->default(0);
            $table->integer('talas_unidades')->default(0);
            $table->integer('podas_unidades')->default(0);

            // Observaciones
            $table->text('observaciones')->nullable();
            $table->text('incidencias')->nullable();

            // Firmas
            $table->string('encargado_firma', 255)->nullable();
            $table->string('encargado_nombre', 150)->nullable();
            $table->string('cliente_firma', 255)->nullable()->comment('ADIF u otro');
            $table->string('cliente_nombre', 150)->nullable();

            // Estado
            $table->enum('estado', ['borrador', 'completado', 'validado'])->default('borrador');

            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();

            $table->unique(['obra_id', 'fecha'], 'unique_parte_obra_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partes_diarios');
    }
};
