<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tableros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->string('color_fondo', 7)->default('#1e40af');
            $table->string('imagen_fondo', 255)->nullable();
            $table->enum('visibilidad', ['todos', 'roles', 'miembros'])->default('miembros');
            $table->json('roles_visibles')->nullable();
            $table->boolean('archivado')->default(false);
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('obra_id')->nullable()->constrained('obras')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tableros');
    }
};
