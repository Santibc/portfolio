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
            $table->enum('tipo', ['sistema', 'proyecto', 'inversion', 'dividendo', 'retiro', 'deposito', 'mensaje', 'alerta', 'marketing']);
            $table->string('titulo', 200);
            $table->text('contenido');
            $table->string('url_accion', 500)->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_at')->nullable();
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('referencia_type', 100)->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('usuario_id');
            $table->index('leida');
            $table->index('tipo');
            $table->index(['referencia_id', 'referencia_type']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificaciones');
    }
};
