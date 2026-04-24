<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_tallas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('talla', 20);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['producto_id', 'talla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_tallas');
    }
};
