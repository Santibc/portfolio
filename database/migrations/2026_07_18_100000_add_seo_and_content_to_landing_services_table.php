<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_services', function (Blueprint $table) {
            $table->string('slug', 200)->nullable()->unique()->after('id');
            $table->string('subtitle', 255)->nullable()->after('title');
            $table->string('short_description', 300)->nullable()->after('subtitle');
            $table->string('hero_image_path', 500)->nullable()->after('short_description');
            $table->longText('content_html')->nullable()->after('description');
            $table->string('meta_title', 150)->nullable()->after('content_html');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords', 500)->nullable()->after('meta_description');
            $table->string('focus_keyword', 100)->nullable()->after('meta_keywords');
            $table->boolean('is_published')->default(true)->after('is_active');

            $table->index('is_published');
        });

        // Backfill slugs for existing services
        $used = [];
        foreach (DB::table('landing_services')->select('id', 'title')->get() as $row) {
            $base = Str::slug($row->title) ?: 'service-' . $row->id;
            $slug = $base;
            $i = 2;
            while (in_array($slug, $used) || DB::table('landing_services')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $used[] = $slug;
            DB::table('landing_services')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('landing_services', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
            $table->dropColumn([
                'slug',
                'subtitle',
                'short_description',
                'hero_image_path',
                'content_html',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'focus_keyword',
                'is_published',
            ]);
        });
    }
};
