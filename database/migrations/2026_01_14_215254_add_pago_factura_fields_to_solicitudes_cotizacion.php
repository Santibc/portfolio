<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar campos para gestión de pagos y facturación
     */
    public function up()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Campos de pago
            $table->enum('estado_pago', ['pendiente', 'parcial', 'pagado'])
                ->default('pendiente')
                ->after('estado');
            $table->enum('metodo_pago', ['transferencia', 'efectivo', 'tarjeta', 'cheque', 'otro'])
                ->nullable()
                ->after('estado_pago');
            $table->string('comprobante_pago')->nullable()->after('metodo_pago');
            $table->decimal('monto_pagado', 12, 2)->default(0)->after('comprobante_pago');
            $table->timestamp('pagado_en')->nullable()->after('monto_pagado');
            $table->foreignId('verificado_por')
                ->nullable()
                ->after('pagado_en')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verificado_en')->nullable()->after('verificado_por');
            $table->text('notas_pago')->nullable()->after('verificado_en');

            // Campos de facturación
            $table->string('numero_factura')->nullable()->unique()->after('notas_pago');
            $table->timestamp('facturada_en')->nullable()->after('numero_factura');
            $table->foreignId('facturada_por')
                ->nullable()
                ->after('facturada_en')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('archivo_factura')->nullable()->after('facturada_por');
            $table->decimal('porcentaje_iva', 5, 2)->nullable()->after('archivo_factura');
            $table->decimal('valor_iva', 12, 2)->nullable()->after('porcentaje_iva');
            $table->string('forma_pago_factura', 50)->nullable()->after('valor_iva');
            $table->date('fecha_vencimiento')->nullable()->after('forma_pago_factura');

            // Índice para búsquedas
            $table->index('estado_pago');
            $table->index('numero_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['estado_pago']);
            $table->dropIndex(['numero_factura']);

            // Eliminar foreign keys
            $table->dropForeign(['verificado_por']);
            $table->dropForeign(['facturada_por']);

            // Eliminar campos de pago
            $table->dropColumn([
                'estado_pago',
                'metodo_pago',
                'comprobante_pago',
                'monto_pagado',
                'pagado_en',
                'verificado_por',
                'verificado_en',
                'notas_pago',
            ]);

            // Eliminar campos de facturación
            $table->dropColumn([
                'numero_factura',
                'facturada_en',
                'facturada_por',
                'archivo_factura',
                'porcentaje_iva',
                'valor_iva',
                'forma_pago_factura',
                'fecha_vencimiento',
            ]);
        });
    }
};
