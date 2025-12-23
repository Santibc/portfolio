<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caducidades_generales', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 100)->comment('seguro_rc, iso, certificacion, etc.');
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_caducidad');
            $table->string('documento_path', 500)->nullable();
            $table->boolean('alerta_activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caducidades_generales');
    }
};
