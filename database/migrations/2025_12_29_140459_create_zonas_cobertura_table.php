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
        Schema::create('zonas_cobertura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nombre'); // Ej: "Zona Norte", "Centro", "Sur"
            $table->text('descripcion')->nullable();
            $table->json('ciudades_ids'); // Array de IDs de ciudades incluidas
            $table->json('barrios')->nullable(); // Array de nombres de barrios específicos
            $table->string('codigo_postal_desde')->nullable();
            $table->string('codigo_postal_hasta')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['empresa_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zonas_cobertura');
    }
};
