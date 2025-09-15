<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminContentManagerController extends Controller
{
    public function index()
    {
        $pages = Page::with('seo')->paginate(10);
        return view('admin.content-manager.index', compact('pages'));
    }

    public function edit($id)
    {
        $page = Page::with('seo')->findOrFail($id);
        return view('admin.content-manager.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'content' => 'required|array',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => 'nullable|string',
            'og_title' => 'nullable|string|max:150',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|max:2048',
            'og_type' => 'nullable|string',
            'og_url' => 'nullable|url|max:500',
            'og_site_name' => 'nullable|string|max:100',
            'twitter_card' => 'nullable|string',
            'twitter_title' => 'nullable|string|max:150',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|image|max:2048',
            'twitter_site' => 'nullable|string|max:50',
            'twitter_creator' => 'nullable|string|max:50',
            'focus_keyword' => 'nullable|string|max:100',
            'breadcrumb_title' => 'nullable|string',
            'sitemap_include' => 'boolean',
            'sitemap_priority' => 'nullable|numeric|between:0.0,1.0',
            'sitemap_changefreq' => 'nullable|string',
        ]);

        $page = Page::findOrFail($id);

        // Actualizar página
        $pageData = [
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'is_active' => $request->boolean('is_active', true),
        ];

        $page->update($pageData);

        // Actualizar o crear SEO
        $seoData = $request->only([
            'meta_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots',
            'og_title', 'og_description', 'og_type', 'og_url', 'og_site_name',
            'twitter_card', 'twitter_title', 'twitter_description', 'twitter_site', 'twitter_creator',
            'focus_keyword', 'breadcrumb_title', 'sitemap_changefreq'
        ]);

        $seoData['sitemap_include'] = $request->boolean('sitemap_include', true);
        $seoData['sitemap_priority'] = $request->sitemap_priority ?? 0.8;
        $seoData['is_active'] = $request->boolean('is_active', true);

        // Manejar imágenes
        if ($request->hasFile('og_image')) {
            if ($page->seo && $page->seo->og_image) {
                Storage::disk('public')->delete($page->seo->og_image);
            }
            $seoData['og_image'] = $request->file('og_image')->store('seo/og', 'public');
        }

        if ($request->hasFile('twitter_image')) {
            if ($page->seo && $page->seo->twitter_image) {
                Storage::disk('public')->delete($page->seo->twitter_image);
            }
            $seoData['twitter_image'] = $request->file('twitter_image')->store('seo/twitter', 'public');
        }

        if ($page->seo) {
            $page->seo->update($seoData);
        } else {
            $seoData['page_id'] = $page->id;
            Seo::create($seoData);
        }

        return redirect()->route('admin.content-manager.edit', $page->id)
                        ->with('success', 'Contenido actualizado correctamente');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('content/images', 'public');
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path
            ]);
        }

        return response()->json(['success' => false], 400);
    }
}
