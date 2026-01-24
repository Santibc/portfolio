<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the unique constraint first
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropUnique(['numero']);
        });

        // Modify column to be nullable using raw SQL
        DB::statement('ALTER TABLE facturas MODIFY numero VARCHAR(50) NULL');

        // Re-add the unique constraint
        Schema::table('facturas', function (Blueprint $table) {
            $table->unique('numero');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropUnique(['numero']);
        });

        DB::statement('ALTER TABLE facturas MODIFY numero VARCHAR(50) NOT NULL');

        Schema::table('facturas', function (Blueprint $table) {
            $table->unique('numero');
        });
    }
};
