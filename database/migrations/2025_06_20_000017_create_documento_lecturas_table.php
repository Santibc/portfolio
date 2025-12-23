<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_lecturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('trabajador_documentos')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->timestamp('fecha_lectura');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('aceptado')->default(false);
            $table->timestamp('created_at')->useCurrent();
            // Sin updated_at ni deleted_at: registro inmutable
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_lecturas');
    }
};
