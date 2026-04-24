<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Datos de envío internacional — opcionales, pueden llenarse al crear
            // la factura o posteriormente cuando el cliente asigne PO / la aerolínea emita AWB.
            $table->string('po_numero', 60)->nullable()->after('observaciones');
            $table->string('awb', 60)->nullable()->after('po_numero');
            $table->string('shipper', 100)->nullable()->after('awb');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['po_numero', 'awb', 'shipper']);
        });
    }
};
