<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasto_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20)->nullable();
            $table->enum('tipo', ['directo', 'indirecto'])->default('directo');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto_categorias');
    }
};
