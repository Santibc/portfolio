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
        // Primero agregar un índice regular para la FK antes de eliminar el unique
        Schema::table('fichajes', function (Blueprint $table) {
            $table->index('trabajador_id', 'idx_fichajes_trabajador_id');
        });

        // Ahora eliminar el constraint único
        Schema::table('fichajes', function (Blueprint $table) {
            $table->dropUnique('unique_fichaje_dia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fichajes', function (Blueprint $table) {
            $table->unique(['trabajador_id', 'fecha'], 'unique_fichaje_dia');
        });

        Schema::table('fichajes', function (Blueprint $table) {
            $table->dropIndex('idx_fichajes_trabajador_id');
        });
    }
};
