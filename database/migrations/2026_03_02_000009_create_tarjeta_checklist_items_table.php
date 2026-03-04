<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjeta_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('tarjeta_checklists')->cascadeOnDelete();
            $table->string('texto', 500);
            $table->boolean('completado')->default(false);
            $table->foreignId('completado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_completado')->nullable();
            $table->unsignedInteger('posicion')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjeta_checklist_items');
    }
};
