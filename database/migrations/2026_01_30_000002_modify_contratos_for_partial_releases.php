<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            // Agregar campos para tracking de liberaciones parciales
            $table->unsignedTinyInteger('porcentaje_total_liberado')->default(0)->after('fecha_liberacion_real');
            $table->decimal('importe_total_liberado', 12, 2)->default(0)->after('porcentaje_total_liberado');
        });

        // Modificar enum de estado_garantia para incluir 'parcialmente_liberada'
        DB::statement("ALTER TABLE contratos MODIFY COLUMN estado_garantia
                       ENUM('pendiente', 'retenida', 'parcialmente_liberada', 'liberada') NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir el enum al estado original
        DB::statement("ALTER TABLE contratos MODIFY COLUMN estado_garantia
                       ENUM('pendiente', 'retenida', 'liberada') NULL");

        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn(['porcentaje_total_liberado', 'importe_total_liberado']);
        });
    }
};
