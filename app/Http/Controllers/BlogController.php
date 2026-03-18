<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\LandingLayoutConfig;
use App\Models\Page;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $layoutConfig = LandingLayoutConfig::first();
        $posts = BlogPost::published()
            ->with('category', 'author', 'tags')
            ->latest('published_at')
            ->paginate(9);
        $categories = BlogCategory::where('is_active', true)->withCount(['posts' => fn($q) => $q->published()])->get();
        $tags = BlogTag::has('posts')->get();
        $recentPosts = BlogPost::published()->latest('published_at')->take(5)->get();

        $page = Page::where('slug', 'blog')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.blog.index', compact('posts', 'categories', 'tags', 'recentPosts', 'layoutConfig', 'seo'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)->published()->with('category', 'author', 'tags')->firstOrFail();
        $post->increment('views_count');

        $layoutConfig = LandingLayoutConfig::first();
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        $seo = null;
        if ($post->page_id) {
            $page = Page::find($post->page_id);
            $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;
        }

        return view('landing_page.blog.show', compact('post', 'relatedPosts', 'layoutConfig', 'seo'));
    }

    public function byCategory($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        $layoutConfig = LandingLayoutConfig::first();
        $posts = BlogPost::published()
            ->where('category_id', $category->id)
            ->with('category', 'author', 'tags')
            ->latest('published_at')
            ->paginate(9);
        $categories = BlogCategory::where('is_active', true)->withCount(['posts' => fn($q) => $q->published()])->get();
        $tags = BlogTag::has('posts')->get();
        $recentPosts = BlogPost::published()->latest('published_at')->take(5)->get();

        $page = Page::where('slug', 'blog')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.blog.index', compact('posts', 'categories', 'tags', 'recentPosts', 'layoutConfig', 'seo', 'category'));
    }

    public function byTag($slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();
        $layoutConfig = LandingLayoutConfig::first();
        $posts = BlogPost::published()
            ->whereHas('tags', fn($q) => $q->where('blog_tags.id', $tag->id))
            ->with('category', 'author', 'tags')
            ->latest('published_at')
            ->paginate(9);
        $categories = BlogCategory::where('is_active', true)->withCount(['posts' => fn($q) => $q->published()])->get();
        $tags = BlogTag::has('posts')->get();
        $recentPosts = BlogPost::published()->latest('published_at')->take(5)->get();

        $page = Page::where('slug', 'blog')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.blog.index', compact('posts', 'categories', 'tags', 'recentPosts', 'layoutConfig', 'seo', 'tag'));
    }
}
