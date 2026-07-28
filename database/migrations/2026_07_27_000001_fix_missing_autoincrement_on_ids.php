<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Restaura AUTO_INCREMENT (y la PRIMARY KEY si tampoco existe) en el `id` de todas las
 * tablas de la app que lo hayan perdido. Necesario en entornos (p. ej. Hostinger) donde
 * la importación del dump dejó los `id` sin auto_increment, provocando el error 1364
 * "Field 'id' doesn't have a default value" al insertar (crear feria, traslados, etc.).
 *
 * Corre sobre la conexión de la app, así que apunta automáticamente a la base correcta.
 * Es seguro: no borra datos y MySQL retoma el contador desde el id máximo actual.
 */
return new class extends Migration
{
    public function up(): void
    {
        $database = DB::getDatabaseName();

        $columnas = DB::select(
            "SELECT TABLE_NAME, COLUMN_TYPE, COLUMN_KEY
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ?
               AND COLUMN_NAME = 'id'
               AND EXTRA NOT LIKE '%auto_increment%'",
            [$database]
        );

        foreach ($columnas as $col) {
            $tabla = $col->TABLE_NAME;
            $tipo = $col->COLUMN_TYPE;
            $agregarPk = $col->COLUMN_KEY === 'PRI' ? '' : 'ADD PRIMARY KEY (`id`), ';

            $sql = "ALTER TABLE `{$tabla}` {$agregarPk}MODIFY `id` {$tipo} NOT NULL AUTO_INCREMENT";

            try {
                DB::statement($sql);
                Log::info("fix_autoincrement: OK -> {$tabla}");
            } catch (\Throwable $e) {
                // No frenar la migración por una tabla; se registra para revisión manual.
                Log::warning("fix_autoincrement: no se pudo arreglar {$tabla}: " . $e->getMessage());
            }
        }
    }

    public function down(): void
    {
        // Reversa intencionalmente vacía: quitar el auto_increment volvería a romper la app.
    }
};
