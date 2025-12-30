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
        Schema::create('direcciones_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('email_cliente'); // Identificador del cliente (por email)
            $table->string('alias')->nullable(); // Casa, Oficina, etc.
            $table->string('nombre_destinatario');
            $table->string('telefono');
            $table->text('direccion');
            $table->foreignId('ciudad_id')->constrained('ciudades');
            $table->text('instrucciones')->nullable();
            $table->boolean('es_predeterminada')->default(false);
            $table->timestamps();

            $table->index(['empresa_id', 'email_cliente']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direcciones_cliente');
    }
};
