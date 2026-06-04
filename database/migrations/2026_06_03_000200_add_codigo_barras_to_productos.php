<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega código de barras al producto (útil para exportaciones / aduanas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('codigo_barras', 100)->nullable()->after('cubicaje_paca');
            $table->index('codigo_barras');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['codigo_barras']);
            $table->dropColumn('codigo_barras');
        });
    }
};
