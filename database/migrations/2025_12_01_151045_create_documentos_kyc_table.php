<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documentos_kyc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->enum('tipo_documento', ['cedula_frontal', 'cedula_trasera', 'rut', 'camara_comercio', 'extracto_bancario', 'prueba_domicilio', 'selfie', 'otro']);
            $table->string('nombre_archivo', 500);
            $table->string('ruta_archivo', 1000);
            $table->string('mime_type', 100);
            $table->integer('tamanio_kb');
            $table->date('fecha_subida');
            $table->enum('estado', ['pendiente_revision', 'aprobado', 'rechazado', 'requiere_reemplazo'])->default('pendiente_revision');
            $table->unsignedBigInteger('revisado_por')->nullable();
            $table->timestamp('revisado_at')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('revisado_por')->references('id')->on('users')->onDelete('set null');

            $table->index('usuario_id');
            $table->index('tipo_documento');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documentos_kyc');
    }
};
