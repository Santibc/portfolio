<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gasto_categoria_id')->constrained('gasto_categorias');
            $table->foreignId('obra_id')->nullable()->constrained('obras')->nullOnDelete();
            $table->string('proveedor', 255)->nullable();

            $table->string('concepto', 255);
            $table->text('descripcion')->nullable();
            $table->decimal('importe', 14, 2);
            $table->decimal('iva_porcentaje', 5, 2)->default(21);
            $table->decimal('iva_importe', 12, 2)->nullable();
            $table->decimal('importe_total', 14, 2);

            $table->date('fecha');
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_pago')->nullable();

            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->string('forma_pago', 100)->nullable();

            $table->string('documento_path', 500)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
