<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->unsignedInteger('precio');
            $table->string('imagen')->nullable();
            $table->foreignId('tipo_id')
                ->constrained('tipos_menu_item')
                ->restrictOnDelete();
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activo', 'tipo_id']);
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
