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
        // Primero eliminar el índice único antiguo
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->dropUnique(['producto_id', 'talla', 'color']);
        });

        // Renombrar columna usando CHANGE COLUMN (compatible con MariaDB)
        DB::statement('ALTER TABLE variantes_productos CHANGE COLUMN talla referencia_variante VARCHAR(255) NULL');

        // Crear el nuevo índice único
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->unique(['producto_id', 'referencia_variante', 'color'], 'variantes_unique_ref_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->dropUnique('variantes_unique_ref_color');
        });

        DB::statement('ALTER TABLE variantes_productos CHANGE COLUMN referencia_variante talla VARCHAR(255) NULL');

        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->unique(['producto_id', 'talla', 'color']);
        });
    }
};
