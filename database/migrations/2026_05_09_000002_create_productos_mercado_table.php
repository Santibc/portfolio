<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_mercado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad_empaque', 50);
            $table->string('imagen')->nullable();
            $table->foreignId('tipo_id')
                ->constrained('tipos_producto_mercado')
                ->restrictOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('activo');
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_mercado');
    }
};
