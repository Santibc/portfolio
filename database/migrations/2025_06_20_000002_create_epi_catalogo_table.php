<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('epi_catalogo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('categoria', 100)->nullable()->comment('Ej: Protección cabeza, Protección altura');
            $table->boolean('tiene_caducidad')->default(false);
            $table->boolean('requiere_revision')->default(false);
            $table->integer('periodicidad_revision_meses')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epi_catalogo');
    }
};
