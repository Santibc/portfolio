<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuraciones_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->string('nombre', 200);
            $table->text('valor')->nullable();
            $table->enum('tipo', ['texto', 'numero', 'decimal', 'booleano', 'fecha', 'json', 'archivo']);
            $table->string('grupo', 100)->default('general');
            $table->text('descripcion')->nullable();
            $table->boolean('editable')->default(true);
            $table->unsignedBigInteger('modificado_por')->nullable();
            $table->timestamp('modificado_at')->nullable();
            $table->timestamps();

            $table->foreign('modificado_por')->references('id')->on('users')->onDelete('set null');

            $table->index('clave');
            $table->index('grupo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuraciones_sistema');
    }
};
