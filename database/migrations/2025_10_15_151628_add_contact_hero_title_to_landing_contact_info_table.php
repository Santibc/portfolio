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
        Schema::table('landing_contact_info', function (Blueprint $table) {
            $table->string('contact_hero_title')->default('Get Your Free Estimate')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_contact_info', function (Blueprint $table) {
            $table->dropColumn('contact_hero_title');
        });
    }
};
