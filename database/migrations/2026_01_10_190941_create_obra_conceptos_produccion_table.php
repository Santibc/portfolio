<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obra_conceptos_produccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            $table->string('codigo', 20);
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->enum('categoria', ['desbroce', 'limpieza', 'herbicida', 'tala', 'poda', 'otro']);
            $table->enum('unidad', ['m2', 'unidades', 'hectareas', 'jornal']);
            $table->decimal('precio_unitario', 10, 2);
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();

            // Índices
            $table->unique(['obra_id', 'codigo'], 'unique_obra_codigo');
            $table->index(['obra_id', 'activo'], 'idx_obra_activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obra_conceptos_produccion');
    }
};
