<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->date('fecha_facturacion_inicio')->nullable()->after('fecha_fin_real');
            $table->date('fecha_facturacion_fin')->nullable()->after('fecha_facturacion_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn(['fecha_facturacion_inicio', 'fecha_facturacion_fin']);
        });
    }
};
