<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->foreignId('factura_siigo_id')->nullable()->after('estado')
                ->constrained('facturas_siigo')->nullOnDelete();
            $table->boolean('requiere_factura')->default(false)->after('factura_siigo_id');
        });
    }

    public function down()
    {
        Schema::table('ventas_pdv', function (Blueprint $table) {
            $table->dropForeign(['factura_siigo_id']);
            $table->dropColumn(['factura_siigo_id', 'requiere_factura']);
        });
    }
};
