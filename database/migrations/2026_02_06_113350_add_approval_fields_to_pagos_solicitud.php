<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pagos_solicitud', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
                  ->default('pendiente')
                  ->after('registrado_por');
            $table->foreignId('aprobado_por')
                  ->nullable()
                  ->after('estado')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('aprobado_en')
                  ->nullable()
                  ->after('aprobado_por');
        });

        // Backfill existing payments as approved (they were already counted)
        DB::table('pagos_solicitud')->update(['estado' => 'aprobado']);
    }

    public function down()
    {
        Schema::table('pagos_solicitud', function (Blueprint $table) {
            $table->dropForeign(['aprobado_por']);
            $table->dropColumn(['aprobado_en', 'aprobado_por', 'estado']);
        });
    }
};
