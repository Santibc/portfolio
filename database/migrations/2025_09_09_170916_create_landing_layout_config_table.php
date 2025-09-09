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
        Schema::create('landing_layout_config', function (Blueprint $table) {
            $table->id();
            $table->string('site_title')->default('Montano&Co');
            $table->string('topbar_email')->default('contacto@ejemplo.com');
            $table->string('topbar_phone')->default('+57 310 000 0000');
            $table->string('twitter_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('footer_address')->default('Calle 108 #10-20');
            $table->string('footer_city')->default('Bogotá, Colombia');
            $table->string('footer_phone')->default('+57 310 000 0000');
            $table->string('footer_email')->default('info@ejemplo.com');
            $table->string('copyright_company')->default('Montano&Co.');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('landing_layout_config');
    }
};
