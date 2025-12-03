<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prospectos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_prospecto', 50)->unique();
            $table->string('nombre', 200);
            $table->string('email', 200)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->enum('tipo', ['inversionista', 'agricultor', 'otro']);
            $table->enum('estado', ['nuevo', 'contactado', 'interesado', 'negociacion', 'convertido', 'descartado'])->default('nuevo');
            $table->enum('origen', ['web', 'referido', 'evento', 'redes_sociales', 'telemarketing', 'otro']);
            $table->unsignedBigInteger('asignado_a')->nullable();
            $table->date('fecha_contacto')->nullable();
            $table->date('fecha_conversion')->nullable();
            $table->unsignedBigInteger('usuario_convertido_id')->nullable();
            $table->text('notas')->nullable();
            $table->json('datos_adicionales')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('asignado_a')->references('id')->on('users')->onDelete('set null');
            $table->foreign('usuario_convertido_id')->references('id')->on('users')->onDelete('set null');

            $table->index('codigo_prospecto');
            $table->index('email');
            $table->index('telefono');
            $table->index('estado');
            $table->index('asignado_a');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prospectos');
    }
};
