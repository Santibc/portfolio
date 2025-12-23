<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obra_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->comment('desbroce, tala, poda, emergencia, mixto');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obra_tipos');
    }
};
