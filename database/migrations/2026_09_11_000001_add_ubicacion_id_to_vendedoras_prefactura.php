<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendedoras_prefactura', function (Blueprint $table) {
            if (!Schema::hasColumn('vendedoras_prefactura', 'ubicacion_id')) {
                $table->unsignedBigInteger('ubicacion_id')->nullable()->after('nombre');
                $table->index('ubicacion_id');
                $table->foreign('ubicacion_id')
                      ->references('id')->on('ubicaciones')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendedoras_prefactura', function (Blueprint $table) {
            if (Schema::hasColumn('vendedoras_prefactura', 'ubicacion_id')) {
                $table->dropForeign(['ubicacion_id']);
                $table->dropIndex(['ubicacion_id']);
                $table->dropColumn('ubicacion_id');
            }
        });
    }
};
