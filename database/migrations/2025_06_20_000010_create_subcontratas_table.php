<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcontratas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('razon_social', 255)->nullable();
            $table->string('cif', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('persona_contacto', 150)->nullable();

            // Tarifas
            $table->decimal('tarifa_hora', 8, 2)->nullable();
            $table->decimal('tarifa_dia', 10, 2)->nullable();

            // Estado
            $table->boolean('activa')->default(true);
            $table->boolean('homologada')->default(false);
            $table->date('fecha_homologacion')->nullable();

            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcontratas');
    }
};
