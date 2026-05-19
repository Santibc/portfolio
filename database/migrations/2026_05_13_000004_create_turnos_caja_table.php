<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_apertura_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('user_cierre_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();
            $table->dateTime('abierto_en');
            $table->unsignedInteger('base_inicial');
            $table->dateTime('cerrado_en')->nullable();
            $table->unsignedInteger('total_declarado')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('cerrado_en');
            $table->index('abierto_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos_caja');
    }
};
