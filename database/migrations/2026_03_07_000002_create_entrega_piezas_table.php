<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('entrega_piezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrega_id');
            $table->unsignedBigInteger('orden_pieza_id');
            $table->integer('cantidad');
            $table->timestamp('created_at')->nullable();

            $table->foreign('entrega_id')->references('id')->on('entregas')->onDelete('cascade');
            $table->foreign('orden_pieza_id')->references('id')->on('orden_piezas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('entrega_piezas');
    }
};
