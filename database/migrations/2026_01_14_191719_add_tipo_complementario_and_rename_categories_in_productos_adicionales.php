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
        Schema::table('productos_adicionales', function (Blueprint $table) {
            // Agregar campo tipo_complementario
            $table->enum('tipo_complementario', ['cross_selling', 'especifico'])
                ->default('cross_selling')
                ->after('categoria')
                ->comment('cross_selling: general para todos, especifico: por tipo de producto');

            // Agregar índice para mejor performance
            $table->index(['tipo_complementario', 'disponible']);
        });

        // Actualizar categorías existentes a nomenclatura correcta
        DB::table('productos_adicionales')->where('categoria', 'chocolate')->update(['categoria' => 'bombones']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos_adicionales', function (Blueprint $table) {
            $table->dropIndex(['tipo_complementario', 'disponible']);
            $table->dropColumn('tipo_complementario');
        });

        // Revertir cambio de categoría
        DB::table('productos_adicionales')->where('categoria', 'bombones')->update(['categoria' => 'chocolate']);
    }
};
