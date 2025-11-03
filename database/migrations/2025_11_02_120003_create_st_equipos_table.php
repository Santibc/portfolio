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
        Schema::create('st_equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('st_cliente_id')->constrained('st_clientes')->onDelete('restrict');
            $table->string('tipo_equipo'); // Cámara IP, Cámara Analógica, DVR, NVR, Control Acceso, etc.
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->unique();
            $table->string('mac_address')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('especificaciones')->nullable(); // Resolución, almacenamiento, canales, etc.
            $table->date('fecha_compra')->nullable();
            $table->date('fecha_instalacion')->nullable();
            $table->boolean('en_garantia')->default(false);
            $table->date('vencimiento_garantia')->nullable();
            $table->text('ubicacion_instalacion')->nullable(); // Dónde está instalado el equipo
            $table->string('estado', 20)->default('operativo'); // operativo, en_reparacion, fuera_servicio
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
        Schema::dropIfExists('st_equipos');
    }
};
