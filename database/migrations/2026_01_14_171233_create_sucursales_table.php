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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('nombre');
            $table->string('direccion');
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades');
            $table->string('telefono')->nullable();
            $table->string('contacto')->nullable();
            $table->string('email')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['cliente_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sucursales');
    }
};
