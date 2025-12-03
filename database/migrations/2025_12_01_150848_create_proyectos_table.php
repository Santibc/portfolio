<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('agricultor_id');
            $table->string('nombre', 200);
            $table->text('descripcion');
            $table->text('ubicacion');
            $table->string('coordenadas', 100)->nullable();

            // Términos financieros
            $table->decimal('monto_objetivo', 15, 2);
            $table->decimal('monto_recaudado', 15, 2)->default(0);
            $table->decimal('inversion_minima', 15, 2);
            $table->decimal('inversion_maxima', 15, 2)->nullable();
            $table->decimal('roi_anual', 5, 2);
            $table->integer('duracion_meses');
            $table->integer('periodo_cosecha_meses')->nullable();
            $table->integer('periodo_dividendos_dias')->default(30);

            // Fechas
            $table->date('fecha_inicio_recaudacion');
            $table->date('fecha_cierre_recaudacion');
            $table->date('fecha_inicio_proyecto')->nullable();
            $table->date('fecha_fin_proyecto')->nullable();
            $table->date('fecha_primer_dividendo')->nullable();

            // Workflow de estados
            $table->enum('estado', ['borrador', 'en_revision', 'rechazado', 'aprobado', 'en_recaudacion', 'fondeado', 'en_ejecucion', 'en_cosecha', 'finalizado', 'cancelado'])->default('borrador');
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->timestamp('aprobado_at')->nullable();
            $table->text('notas_aprobacion')->nullable();
            $table->text('motivo_rechazo')->nullable();

            // Metadatos
            $table->enum('nivel_riesgo', ['bajo', 'medio', 'alto'])->default('medio');
            $table->boolean('verificado')->default(false);
            $table->boolean('destacado')->default(false);
            $table->integer('orden_destacado')->default(0);
            $table->json('datos_adicionales')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('categoria_id')->references('id')->on('categorias_proyecto')->onDelete('restrict');
            $table->foreign('agricultor_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');

            // Índices
            $table->index('codigo');
            $table->index('categoria_id');
            $table->index('agricultor_id');
            $table->index('estado');
            $table->index('fecha_cierre_recaudacion');
            $table->index(['destacado', 'orden_destacado']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyectos');
    }
};
