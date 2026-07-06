<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->string('observacion', 500)
                ->nullable()
                ->after('turno_caja_id');
        });
    }

    public function down(): void
    {
        Schema::table('registros_mercado', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }
};
