<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siigo_config', function (Blueprint $table) {
            // NIT del emisor (sin dígito de verificación). Se usa en el contenido del QR
            // según el Anexo Técnico DIAN 1.9 (campo NitFac).
            $table->string('nit_emisor', 30)->nullable()->after('partner_id');

            // IDs de catálogos Siigo obligatorios al crear facturas electrónicas.
            // Se obtienen sincronizando catálogos o consultando Siigo Nube.
            $table->unsignedBigInteger('tipo_documento_id')->nullable()->after('nit_emisor');
            $table->unsignedBigInteger('seller_id')->nullable()->after('tipo_documento_id');
            $table->unsignedBigInteger('payment_type_id')->nullable()->after('seller_id');
        });
    }

    public function down(): void
    {
        Schema::table('siigo_config', function (Blueprint $table) {
            $table->dropColumn(['nit_emisor', 'tipo_documento_id', 'seller_id', 'payment_type_id']);
        });
    }
};
