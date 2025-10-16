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
        Schema::dropIfExists('landing_carousel_images');
        Schema::dropIfExists('landing_steps');
        Schema::dropIfExists('landing_services');
        Schema::dropIfExists('landing_team_members');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No restoration needed - these tables are obsolete
    }
};
