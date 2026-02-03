<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documentos_empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->enum('categoria', [
                'legal',
                'fiscal',
                'laboral',
                'certificaciones',
                'seguros',
                'contratos',
                'procedimientos',
                'otro'
            ])->default('otro');
            $table->string('archivo_path', 500);
            $table->string('archivo_nombre_original', 255);
            $table->string('archivo_extension', 10);
            $table->bigInteger('archivo_tamaño')->unsigned()->nullable();
            $table->date('fecha_documento')->nullable();
            $table->date('fecha_caducidad')->nullable();
            $table->boolean('visible_solo_admin')->default(true);
            $table->text('notas')->nullable();
            $table->foreignId('subido_por')->constrained('users');
            $table->timestamps();

            $table->index(['categoria', 'created_at']);
            $table->index('fecha_caducidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_empresa');
    }
};
