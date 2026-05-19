<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turno_caja_id')
                ->constrained('turnos_caja')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->unsignedInteger('total');
            $table->unsignedInteger('efectivo_recibido')->default(0);
            $table->unsignedInteger('cambio')->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['turno_caja_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
