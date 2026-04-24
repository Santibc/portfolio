<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puertos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('pais', 80)->default('Colombia');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['pais', 'activo']);
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puertos');
    }
};
