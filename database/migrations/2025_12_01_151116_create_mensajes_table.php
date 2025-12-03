<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('remitente_id');
            $table->unsignedBigInteger('destinatario_id');
            $table->string('asunto', 200);
            $table->text('contenido');
            $table->boolean('leido')->default(false);
            $table->timestamp('leido_at')->nullable();
            $table->boolean('archivado_remitente')->default(false);
            $table->boolean('archivado_destinatario')->default(false);
            $table->unsignedBigInteger('mensaje_padre_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('remitente_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('destinatario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mensaje_padre_id')->references('id')->on('mensajes')->onDelete('set null');

            $table->index('remitente_id');
            $table->index('destinatario_id');
            $table->index('leido');
            $table->index('mensaje_padre_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mensajes');
    }
};
