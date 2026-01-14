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
        Schema::table('categorias', function (Blueprint $table) {
            // tipo: 'ocasion' para ocasiones (cumpleaños, aniversarios)
            //       'formato' para formatos (ramos, arreglos, cajas)
            //       null para categorías legacy sin tipo
            $table->enum('tipo', ['ocasion', 'formato'])->nullable()->after('empresa_id');

            // Agregar índice para búsquedas por tipo
            $table->index(['empresa_id', 'tipo', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropIndex(['empresa_id', 'tipo', 'activo']);
            $table->dropColumn('tipo');
        });
    }
};
