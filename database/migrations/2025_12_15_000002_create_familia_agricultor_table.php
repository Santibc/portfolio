<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('familia_agricultor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agricultor_id');

            $table->enum('parentesco', ['esposa', 'esposo', 'hijo', 'hija', 'padre', 'madre', 'hermano', 'hermana', 'otro'])->default('otro');
            $table->string('nombre', 255);
            $table->integer('edad')->nullable();
            $table->enum('nivel_educativo', ['ninguno', 'primaria', 'secundaria', 'tecnico', 'profesional', 'posgrado'])->nullable();
            $table->enum('estudia_actualmente', ['si', 'no', 'estudio_aplazado'])->nullable();
            $table->boolean('trabaja_en_cultivo')->default(false);

            $table->timestamps();

            // Foreign key
            $table->foreign('agricultor_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index('agricultor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('familia_agricultor');
    }
};
