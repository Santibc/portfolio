<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega "unidades por paca": cuántas unidades de venta entran en una paca.
 * Permite calcular automáticamente el peso/cubicaje total de la cantidad pedida
 * en la cotización:  pacas = cantidad / unidades_por_paca;
 *                    peso_total = pacas * peso_paca;  volumen_total = pacas * cubicaje_paca.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('unidades_por_paca', 12, 3)->nullable()->after('cubicaje_paca');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('unidades_por_paca');
        });
    }
};
