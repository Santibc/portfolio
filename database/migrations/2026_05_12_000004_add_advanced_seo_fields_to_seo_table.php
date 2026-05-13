<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('seo', function (Blueprint $table) {
            $table->string('og_title', 150)->nullable()->after('focus_keyword');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image_path', 500)->nullable()->after('og_description');
            $table->string('og_type', 50)->nullable()->default('website')->after('og_image_path');
            $table->string('twitter_card', 50)->nullable()->default('summary_large_image')->after('og_type');
            $table->string('twitter_title', 150)->nullable()->after('twitter_card');
            $table->text('twitter_description')->nullable()->after('twitter_title');
            $table->string('twitter_image_path', 500)->nullable()->after('twitter_description');
            $table->string('schema_type', 50)->nullable()->default('LocalBusiness')->after('twitter_image_path');
            $table->json('schema_data')->nullable()->after('schema_type');
        });
    }

    public function down()
    {
        Schema::table('seo', function (Blueprint $table) {
            $table->dropColumn([
                'og_title',
                'og_description',
                'og_image_path',
                'og_type',
                'twitter_card',
                'twitter_title',
                'twitter_description',
                'twitter_image_path',
                'schema_type',
                'schema_data',
            ]);
        });
    }
};
