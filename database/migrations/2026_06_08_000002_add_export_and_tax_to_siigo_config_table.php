<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siigo_config', function (Blueprint $table) {
            // Tipo de documento "Factura electrónica de venta – exportación".
            // Se usa cuando la plantilla de la factura es internacional.
            $table->unsignedBigInteger('tipo_documento_export_id')->nullable()->after('tipo_documento_id');

            // ID del impuesto IVA en Siigo (catálogo 'taxes'). Se aplica a los ítems
            // con impuesto en facturas nacionales. La exportación va exenta (sin taxes).
            $table->unsignedBigInteger('tax_id')->nullable()->after('payment_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('siigo_config', function (Blueprint $table) {
            $table->dropColumn(['tipo_documento_export_id', 'tax_id']);
        });
    }
};
