<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega peso por paca y cubicaje (volumen) por paca al producto.
 * Usados en el PDF de cotización (columnas Peso / Volumen) y carga masiva.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('peso_paca', 10, 3)->nullable()->after('unidad_empaque');      // kg por paca
            $table->decimal('cubicaje_paca', 10, 4)->nullable()->after('peso_paca');        // m³ por paca
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['peso_paca', 'cubicaje_paca']);
        });
    }
};
