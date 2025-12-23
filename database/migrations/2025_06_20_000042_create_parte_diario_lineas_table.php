<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parte_diario_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parte_diario_id')->constrained('partes_diarios')->cascadeOnDelete();

            // Tipo de trabajo
            $table->boolean('herbicida')->default(false);
            $table->boolean('desbroce')->default(false);
            $table->boolean('poda')->default(false);
            $table->boolean('tala')->default(false);
            $table->boolean('limpieza')->default(false);

            // Ubicación (puntos kilométricos)
            $table->string('pk_inicio', 20)->nullable();
            $table->string('pk_fin', 20)->nullable();

            // Márgenes y medidas
            $table->boolean('margen_izquierda')->default(false);
            $table->decimal('ancho_izquierda', 8, 2)->nullable();
            $table->boolean('margen_derecha')->default(false);
            $table->decimal('ancho_derecha', 8, 2)->nullable();
            $table->integer('unidades')->nullable()->comment('Para talas/podas');

            // Metros cuadrados calculados
            $table->decimal('metros_cuadrados', 12, 2)->nullable();

            $table->text('observaciones')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parte_diario_lineas');
    }
};
