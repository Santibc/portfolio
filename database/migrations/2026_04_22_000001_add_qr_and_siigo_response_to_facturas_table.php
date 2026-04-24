<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // HTML del QR (incluye el <img src="data:image/png;base64,..."> listo para inyectar en la plantilla)
            // Se genera localmente cuando Siigo devuelve el CUFE, siguiendo el Anexo Técnico DIAN 1.9.
            $table->text('qr_html')->nullable()->after('cufe');

            // URL de consulta DIAN construida con el CUFE (catalogo-vpfe.dian.gov.co/...)
            $table->string('qr_url', 500)->nullable()->after('qr_html');

            // Respuesta cruda de Siigo al crear factura (incluye stamp.status, stamp.errors, metadata, etc.)
            // Útil para debug, reintentos y auditoría.
            $table->json('siigo_response')->nullable()->after('qr_url');

            // Estado del timbrado DIAN según Siigo: Accepted, Rejected, Pending, etc.
            $table->string('stamp_status', 30)->nullable()->after('siigo_response');

            // ID UUID de la factura en Siigo (necesario para GET /v1/invoices/{id}/pdf y otros endpoints)
            $table->string('siigo_id', 64)->nullable()->after('stamp_status');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['qr_html', 'qr_url', 'siigo_response', 'stamp_status', 'siigo_id']);
        });
    }
};
