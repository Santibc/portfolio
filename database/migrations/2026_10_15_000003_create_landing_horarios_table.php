<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_horarios', function (Blueprint $table) {
            $table->id();
            $table->string('dias', 60);
            $table->string('horas', 60);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_horarios');
    }
};
