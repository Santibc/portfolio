<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facturas_siigo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_pdv_id')->constrained('ventas_pdv')->cascadeOnDelete();
            $table->enum('tipo_documento', ['factura_venta', 'nota_credito', 'consumidor_final'])
                ->default('factura_venta');
            $table->unsignedInteger('siigo_document_type_id')->nullable();
            $table->string('siigo_invoice_id')->nullable()->comment('GUID de la factura en SIIGO');
            $table->string('numero_factura')->nullable();
            $table->string('cufe')->nullable()->comment('Código Único Factura Electrónica DIAN');
            $table->date('fecha_emision');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado_dian', ['pendiente', 'aprobada', 'rechazada', 'error'])
                ->default('pendiente');
            $table->enum('estado_envio_email', ['no_enviado', 'enviado', 'error'])
                ->default('no_enviado');
            $table->string('email_destino')->nullable();
            $table->json('siigo_request')->nullable();
            $table->json('siigo_response')->nullable();
            $table->text('errores')->nullable();
            $table->unsignedInteger('intentos')->default(0);
            $table->timestamp('ultimo_intento_en')->nullable();
            $table->foreignId('nota_credito_de')->nullable()
                ->constrained('facturas_siigo')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()
                ->constrained('clientes')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();

            $table->index('estado_dian');
            $table->index('siigo_invoice_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('facturas_siigo');
    }
};
