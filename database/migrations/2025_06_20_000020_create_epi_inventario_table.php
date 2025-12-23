<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('epi_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epi_catalogo_id')->constrained('epi_catalogo');
            $table->string('numero_serie', 100)->nullable();
            $table->date('fecha_compra')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->decimal('coste', 10, 2)->nullable();
            $table->enum('estado', ['disponible', 'asignado', 'en_revision', 'baja'])->default('disponible');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epi_inventario');
    }
};
