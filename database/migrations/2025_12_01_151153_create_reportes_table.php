<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_reporte', 50)->unique();
            $table->unsignedBigInteger('generado_por');
            $table->enum('tipo_reporte', ['inversiones', 'proyectos', 'usuarios', 'dividendos', 'transacciones', 'financiero', 'kyc', 'comercial', 'personalizado']);
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->json('filtros')->nullable();
            $table->json('columnas')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('formato', 20);
            $table->string('ruta_archivo', 1000)->nullable();
            $table->enum('estado', ['generando', 'completado', 'error'])->default('generando');
            $table->text('mensaje_error')->nullable();
            $table->timestamp('generado_at');
            $table->timestamp('expira_at')->nullable();
            $table->timestamps();

            $table->foreign('generado_por')->references('id')->on('users')->onDelete('cascade');

            $table->index('codigo_reporte');
            $table->index('generado_por');
            $table->index('tipo_reporte');
            $table->index('estado');
            $table->index('generado_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reportes');
    }
};
