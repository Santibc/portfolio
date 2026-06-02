<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_semana', function (Blueprint $table) {
            // PK = número ISO-8601 del día (1=Lunes … 7=Domingo) para mapear
            // directo con Carbon::dayOfWeekIso. Es una tabla lookup estática.
            $table->unsignedTinyInteger('id')->primary();
            $table->string('nombre', 20)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_semana');
    }
};
