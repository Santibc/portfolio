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
        Schema::create('landing_home_about', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->nullable();
            $table->string('title')->default('WE ARE GUILLEN CLEANING LLC');
            $table->text('lead_text');
            $table->text('description');
            $table->integer('years_experience')->default(16);
            $table->integer('happy_clients')->default(500);
            $table->integer('client_satisfaction')->default(100);
            $table->string('cta_button_text')->default('Learn More About Us');
            $table->string('cta_button_url')->default('/nosotros');
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
        Schema::dropIfExists('landing_home_about');
    }
};
