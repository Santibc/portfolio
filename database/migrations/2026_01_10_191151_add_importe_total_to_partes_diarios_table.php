<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * IMPORTANTE: NO eliminamos los campos antiguos todavía para permitir migración de datos.
     * Estos serán eliminados en una migración posterior una vez verificada la migración.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partes_diarios', function (Blueprint $table) {
            // Agregar nuevo campo para el importe total calculado
            $table->decimal('importe_total_calculado', 14, 2)->default(0)->after('incidencias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partes_diarios', function (Blueprint $table) {
            $table->dropColumn('importe_total_calculado');
        });
    }
};
