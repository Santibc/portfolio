<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->foreignId('mercado_id')
                ->nullable()
                ->after('producto_mercado_id')
                ->constrained('mercados')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->dropConstrainedForeignId('mercado_id');
        });
    }
};
