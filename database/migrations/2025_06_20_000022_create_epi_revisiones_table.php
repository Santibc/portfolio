<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('epi_revisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epi_inventario_id')->constrained('epi_inventario');
            $table->date('fecha_revision');
            $table->date('proxima_revision')->nullable();
            $table->enum('resultado', ['apto', 'no_apto', 'requiere_reparacion']);
            $table->text('observaciones')->nullable();
            $table->foreignId('realizado_por')->constrained('users');
            $table->string('documento_path', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epi_revisiones');
    }
};
