<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerta_configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 100)->comment('formacion, epi, itv, seguro, contrato, etc.');
            $table->integer('dias_antelacion')->comment('Días antes de caducidad para alertar');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerta_configuraciones');
    }
};
