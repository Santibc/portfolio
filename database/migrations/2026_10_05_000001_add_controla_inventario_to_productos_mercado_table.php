<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos_mercado', function (Blueprint $table) {
            // Default false: los productos existentes (y producción) siguen igual hasta que se marquen.
            $table->boolean('controla_inventario')->default(false)->after('activo');
            $table->index('controla_inventario');
        });
    }

    public function down(): void
    {
        Schema::table('productos_mercado', function (Blueprint $table) {
            $table->dropIndex(['controla_inventario']);
            $table->dropColumn('controla_inventario');
        });
    }
};
