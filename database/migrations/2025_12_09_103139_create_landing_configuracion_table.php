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
        Schema::create('landing_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('logo', 500)->nullable();
            $table->string('favicon', 500)->nullable();
            $table->string('footer_texto', 500)->nullable();
            $table->string('terminos_url', 500)->nullable();
            $table->string('whatsapp_numero', 20)->nullable();
            $table->string('whatsapp_texto', 255)->nullable();
            $table->string('facebook_url', 500)->nullable();
            $table->string('tiktok_url', 500)->nullable();
            $table->string('instagram_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('contacto_email', 255)->nullable();
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
        Schema::dropIfExists('landing_configuracion');
    }
};
