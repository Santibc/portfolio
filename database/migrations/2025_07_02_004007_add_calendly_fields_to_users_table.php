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
        Schema::table('users', function (Blueprint $table) {
            $table->string('uuid')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('locale', 10)->nullable();
            $table->string('time_notation', 10)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('slug')->nullable();
            $table->string('scheduling_url')->nullable();
            $table->string('calendly_uri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'uuid', 'avatar_url', 'locale', 'time_notation',
                'timezone', 'slug', 'scheduling_url', 'calendly_uri'
            ]);
        });
    }
};
