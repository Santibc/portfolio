<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obras', function (Blueprint $table) {
            // Campos para tracking de importes acumulados
            $table->decimal('importe_producido_acumulado', 14, 2)->default(0)->after('margen_previsto');
            $table->decimal('importe_pendiente_acumulado', 14, 2)->default(0)->after('importe_producido_acumulado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn(['importe_producido_acumulado', 'importe_pendiente_acumulado']);
        });
    }
};
