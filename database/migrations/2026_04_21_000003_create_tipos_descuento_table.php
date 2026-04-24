<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_descuento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->enum('alcance', ['linea', 'global'])->default('linea');
            $table->enum('modalidad', ['porcentaje', 'valor_fijo'])->default('porcentaje');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['alcance', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_descuento');
    }
};
