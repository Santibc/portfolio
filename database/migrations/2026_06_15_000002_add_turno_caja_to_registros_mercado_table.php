<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->foreignId('turno_caja_id')
                ->nullable()
                ->after('metodo_pago_id')
                ->constrained('turnos_caja')
                ->nullOnDelete();

            $table->index('turno_caja_id');
        });
    }

    public function down(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->dropConstrainedForeignId('turno_caja_id');
        });
    }
};
