<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajadores_turno', function (Blueprint $table) {
            $table->unsignedInteger('valor_ahorro_default')->default(0)->after('valor_turno_default');
        });
    }

    public function down(): void
    {
        Schema::table('trabajadores_turno', function (Blueprint $table) {
            $table->dropColumn('valor_ahorro_default');
        });
    }
};
