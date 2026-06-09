<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichaje_configuracion', function (Blueprint $table) {
            $table->id();
            $table->boolean('activo')->default(false);
            $table->time('hora_entrada')->default('08:00');
            $table->time('hora_salida')->default('17:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichaje_configuracion');
    }
};
