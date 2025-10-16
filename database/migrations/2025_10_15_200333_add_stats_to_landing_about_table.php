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
        Schema::table('landing_about', function (Blueprint $table) {
            $table->integer('years_experience')->default(16)->after('vision_content');
            $table->integer('happy_clients')->default(500)->after('years_experience');
            $table->integer('client_satisfaction')->default(100)->after('happy_clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_about', function (Blueprint $table) {
            $table->dropColumn(['years_experience', 'happy_clients', 'client_satisfaction']);
        });
    }
};
