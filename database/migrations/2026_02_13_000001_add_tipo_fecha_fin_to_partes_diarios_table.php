<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir columnas solo si no existen (idempotente tras fallo parcial)
        if (!Schema::hasColumn('partes_diarios', 'tipo')) {
            Schema::table('partes_diarios', function (Blueprint $table) {
                $table->enum('tipo', ['diario', 'mensual'])->default('diario')->after('fecha');
                $table->date('fecha_fin')->nullable()->after('tipo');

                $table->index(['obra_id', 'tipo', 'fecha', 'fecha_fin'], 'idx_partes_obra_tipo_fechas');
            });
        }

        // Hacer jornada nullable con SQL directo (evita necesidad de doctrine/dbal)
        DB::statement("ALTER TABLE partes_diarios MODIFY jornada VARCHAR(255) NULL");
    }

    public function down(): void
    {
        Schema::table('partes_diarios', function (Blueprint $table) {
            $table->dropIndex('idx_partes_obra_tipo_fechas');
            $table->dropColumn(['tipo', 'fecha_fin']);
        });

        DB::statement("ALTER TABLE partes_diarios MODIFY jornada VARCHAR(255) NOT NULL DEFAULT 'diurna'");
    }
};
