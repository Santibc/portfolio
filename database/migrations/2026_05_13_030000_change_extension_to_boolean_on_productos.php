<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('tiene_extension')->default(false)->after('extension');
        });

        // Migrar datos: si extension tenía algún texto, asumir que maneja extensión
        DB::statement("UPDATE productos SET tiene_extension = 1 WHERE extension IS NOT NULL AND extension <> ''");

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('extension');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('extension')->nullable()->after('unidad_empaque');
        });

        DB::statement("UPDATE productos SET extension = 'Sí' WHERE tiene_extension = 1");

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('tiene_extension');
        });
    }
};
