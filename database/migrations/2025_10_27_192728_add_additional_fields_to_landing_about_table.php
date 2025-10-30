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
            $table->string('page_subtitle')->nullable()->after('page_title');

            // Stats
            $table->integer('stats_years_experience')->default(16)->after('vision_content');
            $table->integer('stats_happy_clients')->default(500)->after('stats_years_experience');
            $table->integer('stats_client_satisfaction')->default(100)->after('stats_happy_clients');

            // Value 1
            $table->string('value1_icon')->default('bi bi-award')->after('stats_client_satisfaction');
            $table->string('value1_title')->default('Quality Assurance')->after('value1_icon');
            $table->text('value1_description')->nullable()->after('value1_title');

            // Value 2
            $table->string('value2_icon')->default('bi bi-people')->after('value1_description');
            $table->string('value2_title')->default('Customer Focus')->after('value2_icon');
            $table->text('value2_description')->nullable()->after('value2_title');

            // Value 3
            $table->string('value3_icon')->default('bi bi-clock-history')->after('value2_description');
            $table->string('value3_title')->default('Reliability')->after('value3_icon');
            $table->text('value3_description')->nullable()->after('value3_title');
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
            $table->dropColumn([
                'page_subtitle',
                'stats_years_experience',
                'stats_happy_clients',
                'stats_client_satisfaction',
                'value1_icon',
                'value1_title',
                'value1_description',
                'value2_icon',
                'value2_title',
                'value2_description',
                'value3_icon',
                'value3_title',
                'value3_description',
            ]);
        });
    }
};
