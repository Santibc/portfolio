<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjeta_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_id')->constrained('tarjetas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('contenido');
            $table->enum('tipo', ['comentario', 'actividad'])->default('comentario');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjeta_comentarios');
    }
};
