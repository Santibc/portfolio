<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->string('tipo', 100);
            $table->string('titulo', 255);
            $table->text('contenido')->nullable();
            $table->string('url', 500)->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_en')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('usuario_id')->references('id')->on('users');
            $table->index(['usuario_id', 'leida']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificaciones');
    }
};
