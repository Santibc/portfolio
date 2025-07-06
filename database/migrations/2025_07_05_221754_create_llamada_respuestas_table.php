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
// database/migrations/xxxx_xx_xx_xxxxxx_create_llamada_respuestas_table.php
public function up()
{
    Schema::create('llamada_respuestas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('llamada_id')->constrained('llamadas')->onDelete('cascade');
        $table->text('pregunta');
        $table->text('respuesta')->nullable();
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
        Schema::dropIfExists('llamada_respuestas');
    }
};
