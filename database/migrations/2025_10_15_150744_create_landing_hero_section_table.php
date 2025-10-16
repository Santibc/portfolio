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
        Schema::create('landing_hero_section', function (Blueprint $table) {
            $table->id();
            $table->string('subtitle')->default('Top Quality Guaranteed');
            $table->string('hero_image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabla para los 8 valores de la compañía en el hero
        Schema::create('landing_hero_values', function (Blueprint $table) {
            $table->id();
            $table->string('icon_class'); // bi bi-shield-check, etc
            $table->string('title');
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
        Schema::dropIfExists('landing_hero_values');
        Schema::dropIfExists('landing_hero_section');
    }
};
