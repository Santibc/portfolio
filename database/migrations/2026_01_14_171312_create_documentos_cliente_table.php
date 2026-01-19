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
        Schema::create('documentos_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nombre'); // Nombre descriptivo del documento
            $table->string('archivo'); // Ruta del archivo
            $table->string('tipo')->nullable(); // RUT, Cámara de Comercio, etc.
            $table->string('mime_type')->nullable();
            $table->integer('tamano')->nullable(); // Tamaño en bytes
            $table->foreignId('subido_por')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('cliente_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos_cliente');
    }
};
