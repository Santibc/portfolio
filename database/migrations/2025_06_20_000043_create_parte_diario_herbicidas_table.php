<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parte_diario_herbicidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parte_diario_id')->constrained('partes_diarios')->cascadeOnDelete();
            $table->string('producto', 255);
            $table->string('numero_registro', 100)->nullable();
            $table->string('dosificacion', 100)->nullable();
            $table->decimal('cantidad', 10, 2)->nullable();
            $table->string('unidad', 20)->nullable()->comment('litros, kg, etc.');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parte_diario_herbicidas');
    }
};
