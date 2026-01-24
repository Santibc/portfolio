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
        // El índice unique también sirve como índice para la FK, así que primero
        // necesitamos agregar un índice regular antes de eliminar el unique
        Schema::table('partes_diarios', function (Blueprint $table) {
            $table->index('obra_id', 'idx_partes_obra_id');
        });

        // Ahora podemos eliminar el unique constraint
        Schema::table('partes_diarios', function (Blueprint $table) {
            $table->dropUnique('unique_parte_obra_fecha');
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
            $table->unique(['obra_id', 'fecha'], 'unique_parte_obra_fecha');
        });

        Schema::table('partes_diarios', function (Blueprint $table) {
            $table->dropIndex('idx_partes_obra_id');
        });
    }
};
