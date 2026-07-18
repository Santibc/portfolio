<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\LandingService;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function index()
    {
        $now = now()->toAtomString();
        $urls = [];

        // Landing static routes
        $staticRoutes = [
            ['name' => 'welcome',            'changefreq' => 'weekly',  'priority' => '1.0'],
            ['name' => 'nosotros',           'changefreq' => 'monthly', 'priority' => '0.7'],
            ['name' => 'equipo',             'changefreq' => 'monthly', 'priority' => '0.6'],
            ['name' => 'servicios',          'changefreq' => 'weekly',  'priority' => '0.9'],
            ['name' => 'contacto',           'changefreq' => 'monthly', 'priority' => '0.6'],
            ['name' => 'services.calculator','changefreq' => 'weekly',  'priority' => '0.9'],
        ];
        foreach ($staticRoutes as $r) {
            if (\Route::has($r['name'])) {
                $urls[] = [
                    'loc' => route($r['name']),
                    'lastmod' => $now,
                    'changefreq' => $r['changefreq'],
                    'priority' => $r['priority'],
                ];
            }
        }

        // Service pages
        if (Schema::hasTable('landing_services')) {
            $services = LandingService::published()->whereNotNull('slug')->get();
            foreach ($services as $svc) {
                $urls[] = [
                    'loc' => route('services.show', $svc->slug),
                    'lastmod' => optional($svc->updated_at)->toAtomString() ?: $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }
        }

        // Blog index
        if (\Route::has('blog.index')) {
            $urls[] = [
                'loc' => route('blog.index'),
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Blog categories
        if (Schema::hasTable('blog_categories')) {
            foreach (BlogCategory::active()->get() as $cat) {
                $urls[] = [
                    'loc' => route('blog.category', $cat->slug),
                    'lastmod' => optional($cat->updated_at)->toAtomString() ?: $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
        }

        // Blog posts
        if (Schema::hasTable('blog_posts')) {
            foreach (BlogPost::published()->get() as $post) {
                $urls[] = [
                    'loc' => route('blog.show', $post->slug),
                    'lastmod' => optional($post->updated_at)->toAtomString() ?: $now,
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            }
        }

        // Canonical host: match seo-meta.blade.php (no www)
        foreach ($urls as $i => $u) {
            $urls[$i]['loc'] = preg_replace('#^https?://(www\.)?cleanmeadelaide\.au#i', 'https://cleanmeadelaide.au', $u['loc']);
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
