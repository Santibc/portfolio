<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Una feria es un evento comercial temporal con su propia ubicación (inventario),
     * su propia lista de precios (copiada de una base, sin afectar las listas regulares)
     * y su propia caja de POS.
     */
    public function up(): void
    {
        Schema::create('ferias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('ubicacion_id');
            $table->unsignedBigInteger('lista_precio_id');       // lista propia de la feria
            $table->unsignedBigInteger('lista_precio_base_id')->nullable(); // de cuál se copió
            $table->unsignedBigInteger('caja_id')->nullable();   // caja POS de la feria
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['borrador', 'activa', 'cerrada'])->default('borrador');
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('ubicacion_id')->references('id')->on('ubicaciones');
            $table->foreign('lista_precio_id')->references('id')->on('listas_precios');
            $table->foreign('lista_precio_base_id')->references('id')->on('listas_precios')->nullOnDelete();
            $table->foreign('caja_id')->references('id')->on('cajas')->nullOnDelete();
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ferias');
    }
};
