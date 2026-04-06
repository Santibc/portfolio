<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('devoluciones_parciales_pdv', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_pdv_id')->constrained('ventas_pdv')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->text('motivo');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->foreignId('factura_siigo_id')->nullable()->constrained('facturas_siigo')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devoluciones_parciales_pdv');
    }
};
