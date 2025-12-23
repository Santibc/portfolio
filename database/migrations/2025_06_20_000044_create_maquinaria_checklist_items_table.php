<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinaria_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_id')->constrained('maquinaria_checklist_plantillas')->cascadeOnDelete();
            $table->string('categoria', 100)->nullable()->comment('Documentación, Seguridad, Pictogramas, etc.');
            $table->text('descripcion');
            $table->integer('orden')->default(0);
            $table->boolean('obligatorio')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria_checklist_items');
    }
};
