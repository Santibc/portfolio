<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Agregar columnas que vienen de st_clientes
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'tipo_documento')) {
                $table->string('tipo_documento', 20)->nullable()->after('numero_identificacion');
            }
            if (!Schema::hasColumn('clientes', 'razon_social')) {
                $table->string('razon_social', 255)->nullable()->after('nombre_contacto');
            }
            if (!Schema::hasColumn('clientes', 'celular')) {
                $table->string('celular', 50)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('clientes', 'direccion')) {
                $table->text('direccion')->nullable()->after('celular');
            }
            if (!Schema::hasColumn('clientes', 'ciudad_texto')) {
                $table->string('ciudad_texto', 255)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('clientes', 'departamento_texto')) {
                $table->string('departamento_texto', 255)->nullable()->after('ciudad_texto');
            }
            if (!Schema::hasColumn('clientes', 'tipo_cliente')) {
                $table->string('tipo_cliente', 20)->default('particular')->after('departamento_texto');
            }
            if (!Schema::hasColumn('clientes', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('tipo_cliente');
            }
        });

        // 2) Hacer nullable columnas que ST no tiene
        // (email es NOT NULL en clientes; ST puede no tenerlo)
        DB::statement('ALTER TABLE clientes MODIFY email VARCHAR(255) NULL');
        DB::statement('ALTER TABLE clientes MODIFY pais_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE clientes MODIFY ciudad_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE clientes MODIFY vendedor_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE clientes MODIFY lista_precio_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            foreach (['tipo_documento','razon_social','celular','direccion','ciudad_texto','departamento_texto','tipo_cliente','observaciones'] as $col) {
                if (Schema::hasColumn('clientes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        // No revertimos los NOT NULL para evitar errores con datos ya migrados.
    }
};
