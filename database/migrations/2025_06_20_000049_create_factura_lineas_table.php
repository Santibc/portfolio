<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->string('concepto', 255);
            $table->text('descripcion')->nullable();
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('importe', 14, 2);
            $table->integer('orden')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_lineas');
    }
};
