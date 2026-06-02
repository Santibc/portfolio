<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot N:N — qué items de menú se ofrecen cada día de la semana.
        // Si un día no tiene filas aquí, la caja muestra todos los items activos.
        Schema::create('menu_dia', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('dia_semana_id');
            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->foreign('dia_semana_id')
                ->references('id')->on('dias_semana')
                ->cascadeOnDelete();

            $table->unique(['dia_semana_id', 'menu_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_dia');
    }
};
