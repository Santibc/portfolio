<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formacion_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->integer('duracion_horas')->nullable();
            $table->integer('periodicidad_meses')->nullable()->comment('Cada cuántos meses caduca (NULL = no caduca)');
            $table->boolean('obligatoria')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formacion_tipos');
    }
};
