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
        Schema::create('st_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento', 20); // CC, NIT, CE, Pasaporte
            $table->string('numero_documento', 50)->unique();
            $table->string('nombre_completo');
            $table->string('razon_social')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20);
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('departamento')->nullable();
            $table->string('tipo_cliente', 20)->default('particular'); // particular, empresa
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_clientes');
    }
};
