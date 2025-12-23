<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcontrata_documentos_obra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcontrata_id')->constrained('subcontratas')->cascadeOnDelete();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->string('tipo', 100);
            $table->string('nombre', 255);
            $table->string('archivo_path', 500);
            $table->date('fecha_documento')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->boolean('obligatorio')->default(true);
            $table->boolean('verificado')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcontrata_documentos_obra');
    }
};
