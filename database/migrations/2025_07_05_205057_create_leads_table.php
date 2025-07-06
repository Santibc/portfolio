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
// database/migrations/xxxx_xx_xx_xxxxxx_create_leads_table.php
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            
            // ¡CAMBIO CLAVE! Asocia el lead con un usuario (closer).
            // Si el closer se elimina, el lead no se elimina (set null).
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('instagram_user')->nullable();
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
        Schema::dropIfExists('leads');
    }
};
