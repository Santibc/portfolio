<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_id')
                ->constrained('listas_mercado')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('estado', 20)->default('en_progreso')->index();
            $table->dateTime('iniciado_en');
            $table->dateTime('finalizado_en')->nullable();
            $table->timestamps();

            $table->index(['estado', 'iniciado_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mercados');
    }
};
