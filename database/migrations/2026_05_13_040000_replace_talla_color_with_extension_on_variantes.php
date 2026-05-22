<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->string('extension')->nullable()->after('producto_id');
        });

        // Migrar datos: combinar talla y color en extension
        DB::statement("
            UPDATE variantes_productos
            SET extension = TRIM(CONCAT_WS(' / ', NULLIF(talla, ''), NULLIF(color, '')))
        ");

        Schema::table('variantes_productos', function (Blueprint $table) {
            // dropear unique compuesto primero
            $table->dropUnique(['producto_id', 'talla', 'color']);
            $table->dropColumn(['talla', 'color']);
        });
    }

    public function down()
    {
        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->string('talla')->nullable()->after('producto_id');
            $table->string('color')->nullable()->after('talla');
        });

        DB::statement("UPDATE variantes_productos SET talla = extension WHERE extension IS NOT NULL");

        Schema::table('variantes_productos', function (Blueprint $table) {
            $table->unique(['producto_id', 'talla', 'color']);
            $table->dropColumn('extension');
        });
    }
};
