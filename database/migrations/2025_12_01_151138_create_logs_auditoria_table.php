<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logs_auditoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->enum('accion', ['crear', 'actualizar', 'eliminar', 'login', 'logout', 'ver', 'exportar', 'aprobar', 'rechazar', 'otro']);
            $table->string('modelo', 100);
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->text('descripcion');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip', 50);
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('fecha_hora');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');

            $table->index('usuario_id');
            $table->index('accion');
            $table->index('modelo');
            $table->index(['modelo_id', 'modelo']);
            $table->index('fecha_hora');
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs_auditoria');
    }
};
