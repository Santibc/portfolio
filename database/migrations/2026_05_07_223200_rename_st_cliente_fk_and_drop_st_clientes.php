<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Drop FKs antiguos hacia st_clientes
        if (Schema::hasTable('st_equipos') && $this->fkExists('st_equipos', 'st_equipos_st_cliente_id_foreign')) {
            DB::statement('ALTER TABLE st_equipos DROP FOREIGN KEY st_equipos_st_cliente_id_foreign');
        }
        if (Schema::hasTable('st_ordenes_servicio') && $this->fkExists('st_ordenes_servicio', 'st_ordenes_servicio_st_cliente_id_foreign')) {
            DB::statement('ALTER TABLE st_ordenes_servicio DROP FOREIGN KEY st_ordenes_servicio_st_cliente_id_foreign');
        }

        // 2) Renombrar columnas st_cliente_id -> cliente_id (sin doctrine/dbal, usando SQL directo)
        if (Schema::hasTable('st_equipos') && Schema::hasColumn('st_equipos', 'st_cliente_id')) {
            DB::statement('ALTER TABLE st_equipos CHANGE st_cliente_id cliente_id BIGINT UNSIGNED NOT NULL');
        }
        if (Schema::hasTable('st_ordenes_servicio') && Schema::hasColumn('st_ordenes_servicio', 'st_cliente_id')) {
            DB::statement('ALTER TABLE st_ordenes_servicio CHANGE st_cliente_id cliente_id BIGINT UNSIGNED NOT NULL');
        }

        // 3) Crear nuevos FKs hacia clientes(id)
        if (Schema::hasTable('st_equipos')) {
            Schema::table('st_equipos', function (Blueprint $table) {
                $table->foreign('cliente_id', 'st_equipos_cliente_id_foreign')
                      ->references('id')->on('clientes');
            });
        }
        if (Schema::hasTable('st_ordenes_servicio')) {
            Schema::table('st_ordenes_servicio', function (Blueprint $table) {
                $table->foreign('cliente_id', 'st_ordenes_servicio_cliente_id_foreign')
                      ->references('id')->on('clientes');
            });
        }

        // 4) Drop st_clientes
        Schema::dropIfExists('st_clientes');
    }

    public function down(): void
    {
        // No reversible: requiere restaurar desde backup SQL.
    }

    private function fkExists(string $table, string $name): bool
    {
        $row = DB::selectOne(
            "SELECT 1 AS x FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$table, $name]
        );
        return $row !== null;
    }
};
