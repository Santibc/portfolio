<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandingConfiguracion;
use App\Models\LandingCarouselImage;
use App\Models\LandingService;
use App\Models\LandingStep;
use App\Models\LandingContactInfo;
use App\Models\LandingAbout;
use App\Models\LandingTeamMember;
use App\Models\LandingLayoutConfig;
use App\Models\LandingHomeConfig;
use App\Models\LandingHeroValue;
use App\Models\LandingTestimonial;
use App\Models\LandingGalleryImage;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Seo;

class HomeController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function welcome()
    {
        $homeConfig = LandingHomeConfig::first();
        $contactInfo = LandingContactInfo::first();
        $layoutConfig = LandingLayoutConfig::first();
        $heroValues = LandingHeroValue::where('is_active', true)->orderBy('order')->get();
        $testimonials = LandingTestimonial::where('is_active', true)->orderBy('order')->get();
        $services = LandingService::where('is_active', true)->orderBy('order')->get();
        $galleryImages = LandingGalleryImage::where('is_active', true)->orderBy('order')->get();
        $blogPosts = BlogPost::published()->latest('published_at')->take(3)->get();

        $page = Page::where('slug', 'home')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.home', compact(
            'homeConfig', 'contactInfo', 'layoutConfig', 'heroValues',
            'testimonials', 'services', 'galleryImages', 'blogPosts', 'seo'
        ));
    }

    public function nosotros()
    {
        $about = LandingAbout::first();
        $layoutConfig = LandingLayoutConfig::first();
        $teamMembers = LandingTeamMember::orderBy('order')->get();

        $page = Page::where('slug', 'nosotros')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.nosotros', compact('about', 'layoutConfig', 'teamMembers', 'seo'));
    }

    public function servicios()
    {
        $services = LandingService::where('is_active', true)->orderBy('order')->get();
        $layoutConfig = LandingLayoutConfig::first();

        $page = Page::where('slug', 'servicios')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.servicios', compact('services', 'layoutConfig', 'seo'));
    }

    public function servicioDetalle($slug)
    {
        $service = LandingService::where('slug', $slug)->firstOrFail();
        $layoutConfig = LandingLayoutConfig::first();
        $otherServices = LandingService::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->orderBy('order')
            ->take(3)
            ->get();

        $seo = null;
        if ($service->page_id) {
            $page = Page::find($service->page_id);
            $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;
        }

        return view('landing_page.servicio_detalle', compact('service', 'layoutConfig', 'otherServices', 'seo'));
    }

    public function contacto()
    {
        $contactInfo = LandingContactInfo::first();
        $layoutConfig = LandingLayoutConfig::first();

        $page = Page::where('slug', 'contacto')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.contacto', compact('contactInfo', 'layoutConfig', 'seo'));
    }
}
