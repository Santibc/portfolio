<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->nullable()->constrained('obras')->nullOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->nullOnDelete();

            $table->string('concepto', 255);
            $table->text('descripcion')->nullable();
            $table->decimal('importe', 14, 2);
            $table->decimal('iva_porcentaje', 5, 2)->default(21);
            $table->decimal('iva_importe', 12, 2)->nullable();
            $table->decimal('retencion_porcentaje', 5, 2)->default(0);
            $table->decimal('retencion_importe', 12, 2)->nullable();
            $table->decimal('importe_total', 14, 2)->comment('Importe + IVA - Retención');

            $table->date('fecha');
            $table->date('fecha_prevista_cobro')->nullable();
            $table->date('fecha_cobro')->nullable();

            $table->enum('estado', ['pendiente', 'parcial', 'cobrado'])->default('pendiente');
            $table->string('forma_pago', 100)->nullable();

            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
