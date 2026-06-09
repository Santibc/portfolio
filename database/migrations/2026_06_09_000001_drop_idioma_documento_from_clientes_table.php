<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('clientes', 'idioma_documento')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn('idioma_documento');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('clientes', 'idioma_documento')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->enum('idioma_documento', ['es', 'en'])->default('es')->after('tipo_pago_id');
            });
        }
    }
};
