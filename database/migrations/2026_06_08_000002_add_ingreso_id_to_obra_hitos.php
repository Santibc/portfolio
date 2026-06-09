<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obra_hitos', function (Blueprint $table) {
            // Ingreso generado a partir del hito (para no duplicar cobros)
            $table->foreignId('ingreso_id')->nullable()->after('importe_cobro')
                  ->constrained('ingresos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('obra_hitos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ingreso_id');
        });
    }
};
