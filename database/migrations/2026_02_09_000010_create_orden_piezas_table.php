<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orden_piezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('orden_bosquejo_id')->nullable();
            $table->string('nombre', 255);
            $table->string('nombre_automatico', 255)->nullable();
            $table->integer('cantidad');
            $table->string('material', 100)->nullable();
            $table->string('calibre', 50)->nullable();
            $table->text('especificacion')->nullable();
            $table->decimal('porcentaje_avance', 5, 2)->default(0);
            $table->unsignedBigInteger('operario_actual_id')->nullable();
            $table->string('estado', 50)->default('pendiente');
            $table->boolean('entregada')->default(false);
            $table->timestamp('entregada_en')->nullable();
            $table->unsignedBigInteger('entregada_por')->nullable();
            $table->integer('orden_visual')->default(0);
            $table->timestamps();

            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('orden_bosquejo_id')->references('id')->on('orden_bosquejos')->onDelete('set null');
            $table->foreign('operario_actual_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('entregada_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orden_piezas');
    }
};
