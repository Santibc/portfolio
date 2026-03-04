<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('columna_id')->constrained('tablero_columnas')->cascadeOnDelete();
            $table->string('titulo', 500);
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('posicion')->default(0);
            $table->dateTime('fecha_vencimiento')->nullable();
            $table->dateTime('fecha_completada')->nullable();
            $table->enum('prioridad', ['alta', 'media', 'baja'])->default('media');
            $table->string('color_portada', 7)->nullable();
            $table->boolean('archivada')->default(false);
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjetas');
    }
};
