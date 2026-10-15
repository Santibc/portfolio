<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pasos de "Del horno a tu mesa".
        Schema::create('landing_pasos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 60);
            $table->string('texto', 300);
            $table->string('imagen')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pasos');
    }
};
