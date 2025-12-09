<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_eventos', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 200)->unique();
            $table->string('titulo', 255);
            $table->text('extracto')->nullable();
            $table->text('contenido')->nullable();
            $table->string('imagen', 500)->nullable();
            $table->string('fecha', 100)->nullable();
            $table->string('hora', 100)->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->string('precio', 100)->nullable();
            $table->enum('estado', ['proximo', 'activo', 'finalizado'])->default('proximo');
            $table->json('incluye')->nullable();
            $table->json('galeria')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_eventos');
    }
};
