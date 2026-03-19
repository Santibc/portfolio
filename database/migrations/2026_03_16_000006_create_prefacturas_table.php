<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prefacturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_prefactura')->unique();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('nombre_cliente')->nullable();
            $table->foreignId('lista_precio_id')->constrained('listas_precios');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('descuento_global', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('estado', ['pendiente', 'aceptada', 'anulada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_creador_id')->constrained('users');
            $table->foreignId('usuario_cajero_id')->nullable()->constrained('users');
            $table->foreignId('venta_pdv_id')->nullable()->constrained('ventas_pdv')->nullOnDelete();
            $table->text('motivo_anulacion')->nullable();
            $table->foreignId('anulada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('anulada_en')->nullable();
            $table->timestamp('aceptada_en')->nullable();
            $table->timestamps();
        });

        // Add FK on ventas_pdv for prefactura_id now that table exists
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->foreign('prefactura_id')->references('id')->on('prefacturas')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->dropForeign(['prefactura_id']);
        });
        Schema::dropIfExists('prefacturas');
    }
};
