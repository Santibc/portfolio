<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('author_name', 120)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content_html');
            $table->string('cover_image_path', 500)->nullable();

            // SEO
            $table->string('meta_title', 150)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('focus_keyword', 100)->nullable();

            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('set null');
            $table->index('slug');
            $table->index('is_published');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
