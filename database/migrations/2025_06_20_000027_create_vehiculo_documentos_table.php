<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculo_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->enum('tipo', ['ficha_tecnica', 'permiso_circulacion', 'seguro', 'itv', 'otro']);
            $table->string('nombre', 255);
            $table->string('archivo_path', 500);
            $table->date('fecha_documento')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculo_documentos');
    }
};
