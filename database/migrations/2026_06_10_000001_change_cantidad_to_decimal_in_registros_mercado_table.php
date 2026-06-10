<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (suite de tests) es de tipado dinámico: la columna acepta
        // decimales sin necesidad de alterarla. doctrine/dbal 4 es incompatible
        // con ->change() en Laravel 9, así que en MySQL/MariaDB usamos ALTER crudo.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE registros_mercado MODIFY cantidad DECIMAL(10,2) NOT NULL');
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE registros_mercado MODIFY cantidad INT UNSIGNED NOT NULL');
    }
};
