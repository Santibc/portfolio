<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla pivot para asignación de cursos a estudiantes por el admin
     */
    public function up(): void
    {
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('order')->default(0); // Orden del curso para este usuario
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null'); // Admin que asignó
            $table->timestamp('assigned_at')->useCurrent();

            $table->unique(['course_id', 'user_id']);
            $table->index(['user_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_user');
    }
};
