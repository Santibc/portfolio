<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_tipo_id')->constrained('contrato_tipos');
            $table->string('codigo', 50)->unique()->nullable();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();

            // Partes
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('subcontrata_id')->nullable()->constrained('subcontratas')->nullOnDelete();

            // Fechas
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_firma')->nullable();

            // Económico
            $table->decimal('importe', 14, 2)->nullable();
            $table->decimal('iva_porcentaje', 5, 2)->default(21);

            // Retenciones de garantía
            $table->boolean('tiene_retencion')->default(false);
            $table->decimal('retencion_porcentaje', 5, 2)->nullable();
            $table->decimal('importe_retenido', 12, 2)->nullable();
            $table->date('fecha_liberacion_garantia')->nullable();

            // Estado
            $table->enum('estado', ['borrador', 'activo', 'vencido', 'cancelado'])->default('borrador');

            // Documento
            $table->string('documento_path', 500)->nullable();

            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
