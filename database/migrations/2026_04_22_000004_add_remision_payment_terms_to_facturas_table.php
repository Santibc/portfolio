<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Datos adicionales que se muestran en la plantilla PDF ({{factura.remision}}
            // y {{factura.payment_terms}}). Opcionales — pueden llenarse al crear o editar.
            $table->string('remision', 60)->nullable()->after('shipper');
            $table->string('payment_terms', 100)->nullable()->after('remision');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['remision', 'payment_terms']);
        });
    }
};
