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
        Schema::create('landing_about', function (Blueprint $table) {
            $table->id();
            $table->string('page_title')->default('Nosotros');
            $table->string('main_image_path')->nullable();
            $table->string('purpose_title')->default('Propósito');
            $table->text('purpose_content');
            $table->string('mission_title')->default('Misión');
            $table->text('mission_content');
            $table->string('vision_title')->default('Visión');
            $table->text('vision_content');
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
        Schema::dropIfExists('landing_about');
    }
};
