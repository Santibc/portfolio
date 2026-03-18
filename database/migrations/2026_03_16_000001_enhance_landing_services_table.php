<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_services', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('title');
            $table->text('short_description')->nullable()->after('description');
            $table->longText('long_description')->nullable()->after('short_description');
            $table->string('image_path')->nullable()->after('long_description');
            $table->json('gallery_images')->nullable()->after('image_path');
            $table->string('featured_image_alt')->nullable()->after('gallery_images');
            $table->unsignedBigInteger('page_id')->nullable()->after('featured_image_alt');

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('landing_services', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropColumn(['slug', 'short_description', 'long_description', 'image_path', 'gallery_images', 'featured_image_alt', 'page_id']);
        });
    }
};
