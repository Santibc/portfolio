<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinaria_inspeccion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspeccion_id')->constrained('maquinaria_inspecciones')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('maquinaria_checklist_items');
            $table->boolean('cumple')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria_inspeccion_items');
    }
};
