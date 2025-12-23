<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique()->comment('Formato: F-2025-00001');
            $table->string('serie', 10)->default('F');

            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('obra_id')->nullable()->constrained('obras')->nullOnDelete();

            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();

            // Totales
            $table->decimal('base_imponible', 14, 2);
            $table->decimal('iva_porcentaje', 5, 2)->default(21);
            $table->decimal('iva_importe', 12, 2);
            $table->decimal('retencion_porcentaje', 5, 2)->default(0);
            $table->decimal('retencion_importe', 12, 2)->default(0);
            $table->decimal('total', 14, 2);

            // Estado
            $table->enum('estado', ['borrador', 'emitida', 'enviada', 'cobrada', 'anulada'])->default('borrador');
            $table->date('fecha_cobro')->nullable();

            // PDF
            $table->string('pdf_path', 500)->nullable();

            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
