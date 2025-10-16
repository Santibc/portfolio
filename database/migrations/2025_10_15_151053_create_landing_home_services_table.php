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
        Schema::create('landing_home_services', function (Blueprint $table) {
            $table->id();
            $table->string('section_title')->default('Our Services');
            $table->string('section_description')->default('Professional cleaning solutions for your residential and commercial needs');
            $table->string('commercial_title')->default('Commercial Cleaning Services');
            $table->string('residential_title')->default('Residential Cleaning Services');
            $table->text('eco_friendly_note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla para los servicios individuales
        Schema::create('landing_service_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['commercial', 'residential']);
            $table->string('icon_class');
            $table->string('title');
            $table->text('description');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla para las imágenes de los servicios
        Schema::create('landing_service_images', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['commercial', 'residential']);
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('landing_service_images');
        Schema::dropIfExists('landing_service_items');
        Schema::dropIfExists('landing_home_services');
    }
};
