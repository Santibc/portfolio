<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinaria_mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maquinaria_id')->constrained('maquinaria')->cascadeOnDelete();
            $table->enum('tipo', ['preventivo', 'correctivo']);
            $table->date('fecha');
            $table->text('descripcion');
            $table->decimal('coste', 10, 2)->nullable();
            $table->string('proveedor', 255)->nullable();
            $table->string('realizado_por', 255)->nullable();
            $table->date('proxima_revision')->nullable();
            $table->string('documento_path', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinaria_mantenimientos');
    }
};
