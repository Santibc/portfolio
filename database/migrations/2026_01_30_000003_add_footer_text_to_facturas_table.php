<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->text('footer_text')
                  ->nullable()
                  ->default('MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona')
                  ->after('notas')
                  ->comment('Texto personalizado para el pie de página del PDF');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('footer_text');
        });
    }
};
