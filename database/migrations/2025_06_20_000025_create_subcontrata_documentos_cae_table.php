<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcontrata_documentos_cae', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcontrata_id')->constrained('subcontratas')->cascadeOnDelete();
            $table->string('tipo', 100)->comment('TC1, TC2, Seguro RC, etc.');
            $table->string('nombre', 255);
            $table->string('archivo_path', 500);
            $table->date('fecha_documento')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->boolean('verificado')->default(false);
            $table->foreignId('verificado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcontrata_documentos_cae');
    }
};
