<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1) Agregar columnas nuevas (texto)
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nombre_empresa')->nullable()->after('nombre_contacto');
            $table->string('pais')->nullable()->after('telefono');
            $table->string('ciudad')->nullable()->after('pais');
            $table->softDeletes();
        });

        // 2) Migrar datos: copiar nombres desde paises/ciudades a las nuevas columnas
        if (Schema::hasTable('paises')) {
            DB::statement("
                UPDATE clientes c
                INNER JOIN paises p ON p.id = c.pais_id
                SET c.pais = p.nombre
            ");
        }
        if (Schema::hasTable('ciudades')) {
            DB::statement("
                UPDATE clientes c
                INNER JOIN ciudades ci ON ci.id = c.ciudad_id
                SET c.ciudad = ci.nombre
            ");
        }

        // 3) Dropear FKs y columnas viejas
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['pais_id']);
            $table->dropForeign(['ciudad_id']);
            $table->dropColumn(['pais_id', 'ciudad_id']);
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->foreignId('pais_id')->nullable()->after('telefono')->constrained('paises')->onDelete('cascade');
            $table->foreignId('ciudad_id')->nullable()->after('pais_id')->constrained('ciudades')->onDelete('cascade');
            $table->dropColumn(['nombre_empresa', 'pais', 'ciudad']);
            $table->dropSoftDeletes();
        });
    }
};
