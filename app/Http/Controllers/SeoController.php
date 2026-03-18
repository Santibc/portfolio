<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\BlogPost;
use App\Models\LandingService;

class SeoController extends Controller
{
    public function sitemap()
    {
        $pages = Page::where('page_type', 'landing')->get();
        $services = LandingService::where('is_active', true)->get();
        $posts = BlogPost::published()->get();

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static pages
        $staticPages = [
            ['url' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => url('/nosotros'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => url('/servicios'), 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => url('/contacto'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => url('/blog'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        foreach ($staticPages as $page) {
            $content .= '  <url>' . "\n";
            $content .= '    <loc>' . $page['url'] . '</loc>' . "\n";
            $content .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
            $content .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
            $content .= '  </url>' . "\n";
        }

        // Service pages
        foreach ($services as $service) {
            if ($service->slug) {
                $content .= '  <url>' . "\n";
                $content .= '    <loc>' . url('/servicios/' . $service->slug) . '</loc>' . "\n";
                $content .= '    <lastmod>' . $service->updated_at->toW3cString() . '</lastmod>' . "\n";
                $content .= '    <changefreq>monthly</changefreq>' . "\n";
                $content .= '    <priority>0.9</priority>' . "\n";
                $content .= '  </url>' . "\n";
            }
        }

        // Blog posts
        foreach ($posts as $post) {
            $content .= '  <url>' . "\n";
            $content .= '    <loc>' . url('/blog/' . $post->slug) . '</loc>' . "\n";
            $content .= '    <lastmod>' . $post->updated_at->toW3cString() . '</lastmod>' . "\n";
            $content .= '    <changefreq>weekly</changefreq>' . "\n";
            $content .= '    <priority>0.8</priority>' . "\n";
            $content .= '  </url>' . "\n";
        }

        $content .= '</urlset>';

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
