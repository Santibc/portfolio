<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garantias', function (Blueprint $table) {
            $table->text('observacion_creacion')->nullable()->after('tipo_otro_descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('garantias', function (Blueprint $table) {
            $table->dropColumn('observacion_creacion');
        });
    }
};
