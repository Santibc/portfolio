<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Los items y productos ya se eliminan con soft delete y siguen apuntando a su
        // categoría: borrarla de verdad rompía la FK (error 500).
        foreach (['tipos_menu_item', 'tipos_producto_mercado'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (['tipos_menu_item', 'tipos_producto_mercado'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
