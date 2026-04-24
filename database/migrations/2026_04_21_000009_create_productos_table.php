<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('referencia', 40)->unique();
            $table->string('descripcion', 150);
            $table->string('color', 60)->nullable();
            $table->string('composicion', 255)->nullable();
            $table->string('codigo_pa', 20)->nullable();
            $table->decimal('precio_unitario', 14, 2);
            $table->foreignId('moneda_id')->constrained('monedas')->restrictOnDelete();
            $table->foreignId('impuesto_id')->nullable()->constrained('impuestos')->nullOnDelete();
            $table->string('unidad_medida', 20)->default('Und');
            $table->string('imagen_path', 255)->nullable();
            $table->boolean('es_prenda')->default(false);
            $table->boolean('activo')->default(true);
            $table->string('siigo_id', 100)->nullable();
            $table->timestamps();

            $table->index('descripcion');
            $table->index('activo');
            $table->index(['activo', 'es_prenda']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
