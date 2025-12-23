<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajador_formaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->foreignId('formacion_tipo_id')->constrained('formacion_tipos');
            $table->date('fecha_realizacion');
            $table->date('fecha_caducidad')->nullable();
            $table->string('centro_formacion', 255)->nullable();
            $table->string('certificado_path', 500)->nullable()->comment('Solo visible para admin');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajador_formaciones');
    }
};
