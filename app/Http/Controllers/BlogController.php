<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\LandingLayoutConfig;
use App\Models\LandingService;
use App\Models\Seo;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $layoutConfig = LandingLayoutConfig::first();
        $categories = BlogCategory::active()->orderBy('order')->get();

        $query = BlogPost::published()->with('category');
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%")
                    ->orWhere('content_html', 'like', "%{$q}%");
            });
        }
        $posts = $query->orderByDesc('published_at')->paginate(9)->withQueryString();

        $seo = new Seo([
            'meta_title' => 'Blog | ' . ($layoutConfig->site_title ?? 'Clean Me Adelaide'),
            'meta_description' => 'Cleaning tips, guides and news from Clean Me Adelaide. Learn how to keep your home or business spotless.',
            'robots' => 'index,follow',
            'og_title' => 'Blog',
            'og_description' => 'Cleaning tips and guides in Adelaide.',
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'schema_type' => 'Blog',
            'is_active' => true,
        ]);

        return view('landing_page.blog.index', compact(
            'posts', 'categories', 'layoutConfig', 'seo'
        ));
    }

    public function category(Request $request, string $slug)
    {
        $layoutConfig = LandingLayoutConfig::first();
        $categories = BlogCategory::active()->orderBy('order')->get();
        $category = BlogCategory::active()->where('slug', $slug)->firstOrFail();

        $posts = BlogPost::published()
            ->with('category')
            ->where('category_id', $category->id)
            ->orderByDesc('published_at')
            ->paginate(9);

        $seo = new Seo([
            'meta_title' => $category->meta_title ?: ($category->name . ' | Blog | ' . ($layoutConfig->site_title ?? 'Clean Me Adelaide')),
            'meta_description' => $category->meta_description ?: $category->description ?: ('Articles in the ' . $category->name . ' category.'),
            'robots' => 'index,follow',
            'og_title' => $category->name,
            'og_description' => $category->description,
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'is_active' => true,
        ]);

        return view('landing_page.blog.category', compact(
            'category', 'posts', 'categories', 'layoutConfig', 'seo'
        ));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()->with('category')->where('slug', $slug)->firstOrFail();
        $post->increment('views_count');

        $layoutConfig = LandingLayoutConfig::first();
        $categories = BlogCategory::active()->orderBy('order')->get();

        // Related posts (same category, otherwise latest)
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // Internal linking: pull 3 published services to link to from the article
        $relatedServices = LandingService::published()->orderBy('order')->limit(3)->get();

        $defaultExcerpt = strip_tags($post->excerpt ?: '');
        $schemaType = $post->schema_type ?: 'BlogPosting';
        $customSchema = is_array($post->schema_data) ? $post->schema_data : null;

        $autoSchema = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'headline' => $post->title,
            'description' => $defaultExcerpt,
            'image' => $post->cover_image_path ? asset($post->cover_image_path) : null,
            'datePublished' => optional($post->published_at)->toIso8601String(),
            'dateModified' => optional($post->updated_at)->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author_name ?: ($layoutConfig->site_title ?? 'Clean Me Adelaide'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $layoutConfig->site_title ?? 'Clean Me Adelaide',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => route('blog.show', $post->slug),
        ];

        $seo = new Seo([
            'meta_title' => $post->meta_title ?: ($post->title . ' | ' . ($layoutConfig->site_title ?? 'Clean Me Adelaide')),
            'meta_description' => $post->meta_description ?: $defaultExcerpt,
            'meta_keywords' => $post->meta_keywords,
            'canonical_url' => $post->canonical_url,
            'robots' => $post->robots ?: 'index,follow',
            'focus_keyword' => $post->focus_keyword,
            'og_title' => $post->og_title ?: ($post->meta_title ?: $post->title),
            'og_description' => $post->og_description ?: ($post->meta_description ?: $defaultExcerpt),
            'og_image_path' => $post->og_image_path ?: $post->cover_image_path,
            'og_type' => $post->og_type ?: 'article',
            'twitter_card' => $post->twitter_card ?: 'summary_large_image',
            'twitter_title' => $post->twitter_title ?: ($post->og_title ?: $post->title),
            'twitter_description' => $post->twitter_description ?: ($post->og_description ?: $defaultExcerpt),
            'twitter_image_path' => $post->twitter_image_path ?: ($post->og_image_path ?: $post->cover_image_path),
            'schema_type' => $schemaType,
            'schema_data' => $customSchema ?: $autoSchema,
            'is_active' => true,
        ]);

        return view('landing_page.blog.show', compact(
            'post', 'relatedPosts', 'relatedServices', 'categories', 'layoutConfig', 'seo'
        ));
    }
}
