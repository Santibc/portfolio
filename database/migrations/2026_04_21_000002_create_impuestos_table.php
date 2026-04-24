<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impuestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->decimal('porcentaje', 5, 2);
            $table->enum('tipo', ['iva', 'retencion', 'otro'])->default('iva');
            $table->string('codigo_siigo', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['tipo', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impuestos');
    }
};
