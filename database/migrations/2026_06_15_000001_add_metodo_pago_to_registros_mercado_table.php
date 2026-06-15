<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->foreignId('metodo_pago_id')
                ->nullable()
                ->after('valor')
                ->constrained('metodos_pago')
                ->nullOnDelete();

            $table->index('metodo_pago_id');
        });
    }

    public function down(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->dropConstrainedForeignId('metodo_pago_id');
        });
    }
};
