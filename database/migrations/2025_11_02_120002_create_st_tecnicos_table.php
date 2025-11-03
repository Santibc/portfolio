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
        Schema::create('st_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre_completo');
            $table->string('documento', 50)->unique();
            $table->string('email')->unique();
            $table->string('telefono', 20);
            $table->string('celular', 20);
            $table->string('especialidad')->nullable(); // CCTV, Control Acceso, Alarmas, Redes
            $table->text('certificaciones')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->date('fecha_ingreso')->nullable();
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
        Schema::dropIfExists('st_tecnicos');
    }
};
