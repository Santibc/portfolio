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
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Modificar el ENUM para incluir 'rechazada'
            DB::statement("ALTER TABLE solicitudes_cotizacion MODIFY COLUMN estado ENUM('pendiente', 'aplicada', 'rechazada') DEFAULT 'pendiente'");

            // Agregar campos de rechazo
            $table->text('motivo_rechazo')->nullable()->after('observaciones_admin');
            $table->datetime('rechazada_en')->nullable()->after('aplicada_por');
            $table->foreignId('rechazada_por')->nullable()->constrained('users')->after('rechazada_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitudes_cotizacion', function (Blueprint $table) {
            // Eliminar campos de rechazo
            $table->dropForeign(['rechazada_por']);
            $table->dropColumn(['motivo_rechazo', 'rechazada_en', 'rechazada_por']);

            // Revertir ENUM al estado original
            DB::statement("ALTER TABLE solicitudes_cotizacion MODIFY COLUMN estado ENUM('pendiente', 'aplicada') DEFAULT 'pendiente'");
        });
    }
};
