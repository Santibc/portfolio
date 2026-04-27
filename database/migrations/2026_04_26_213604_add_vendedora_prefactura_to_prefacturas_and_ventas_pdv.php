<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('prefacturas', function (Blueprint $table) {
            $table->string('vendedora_prefactura', 100)->nullable()->after('usuario_creador_id');
        });

        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->string('vendedora_prefactura', 100)->nullable()->after('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::table('prefacturas', function (Blueprint $table) {
            $table->dropColumn('vendedora_prefactura');
        });

        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->dropColumn('vendedora_prefactura');
        });
    }
};
