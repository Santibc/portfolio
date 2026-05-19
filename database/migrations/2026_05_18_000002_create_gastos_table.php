<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turno_caja_id')
                ->constrained('turnos_caja')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('tipo', 20);
            $table->foreignId('trabajador_turno_id')
                ->nullable()
                ->constrained('trabajadores_turno')
                ->restrictOnDelete();
            $table->unsignedInteger('valor');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['turno_caja_id', 'created_at']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
