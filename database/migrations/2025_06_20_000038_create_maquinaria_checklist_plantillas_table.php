<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinaria_checklist_plantillas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquinaria_tipo_id')->nullable()->constrained('maquinaria_tipos')->comment('NULL = genérico para todos');
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria_checklist_plantillas');
    }
};
