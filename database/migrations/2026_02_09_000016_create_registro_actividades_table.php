<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registro_actividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('orden_id')->nullable();
            $table->string('accion', 100);
            $table->text('descripcion');
            $table->json('datos_extra')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('usuario_id')->references('id')->on('users');
            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('set null');

            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registro_actividades');
    }
};
