<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tablero_miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tablero_id')->constrained('tableros')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('rol', ['propietario', 'editor', 'observador'])->default('editor');
            $table->timestamps();

            $table->unique(['tablero_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tablero_miembros');
    }
};
