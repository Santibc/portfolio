<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creada_por')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('tipo', 20);                 // TipoPeriodo
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('descripcion', 160);
            $table->unsignedSmallInteger('dias')->default(15);
            $table->string('estado', 20)->default('borrador');  // EstadoNomina
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['fecha_inicio', 'fecha_fin']);
            $table->index('estado');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};
