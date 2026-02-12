<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factura_lineas', function (Blueprint $table) {
            $table->string('grupo', 255)->nullable()->after('orden');
        });
    }

    public function down(): void
    {
        Schema::table('factura_lineas', function (Blueprint $table) {
            $table->dropColumn('grupo');
        });
    }
};
