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
        Schema::create('landing_home_configs', function (Blueprint $table) {
            $table->id();

            // Hero Section
            $table->string('hero_title')->default('CLEAN ME');
            $table->string('hero_subtitle')->default('Top Quality Guaranteed');
            $table->text('hero_description')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->string('hero_services_button_url')->default('/servicios');
            $table->string('hero_estimate_button_url')->default('#contact');

            // About Section
            $table->string('about_title')->default('WE ARE CLEAN ME');
            $table->text('about_lead')->nullable();
            $table->text('about_description')->nullable();
            $table->string('about_image_path')->nullable();
            $table->integer('about_years_experience')->default(16);
            $table->integer('about_happy_clients')->default(500);
            $table->integer('about_client_satisfaction')->default(100);

            // Social Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('youtube_url')->nullable();

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
        Schema::dropIfExists('landing_home_configs');
    }
};
