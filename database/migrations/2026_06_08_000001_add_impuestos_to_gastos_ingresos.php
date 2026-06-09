<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            // IRPF (retención practicada en facturas de gasto)
            $table->decimal('irpf_porcentaje', 5, 2)->default(0)->after('iva_importe');
            $table->decimal('irpf_importe', 12, 2)->default(0)->after('irpf_porcentaje');
            // Desglose de varios tipos de IVA en una misma entrada: [{base, porcentaje, importe}]
            $table->json('desglose_iva')->nullable()->after('irpf_importe');
        });

        Schema::table('ingresos', function (Blueprint $table) {
            $table->json('desglose_iva')->nullable()->after('iva_importe');
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropColumn(['irpf_porcentaje', 'irpf_importe', 'desglose_iva']);
        });

        Schema::table('ingresos', function (Blueprint $table) {
            $table->dropColumn(['desglose_iva']);
        });
    }
};
