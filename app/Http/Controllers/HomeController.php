<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametros;
use App\Models\LandingConfiguracion;
use App\Models\LandingCarouselImage;
use App\Models\LandingService;
use App\Models\LandingStep;
use App\Models\LandingContactInfo;
use App\Models\LandingAbout;
use App\Models\LandingTeamMember;
use App\Models\LandingLayoutConfig;
use App\Models\Page;
use App\Models\Seo;
use App\Models\LandingPricingConfig;
use App\Models\LandingPricingRange;

class HomeController extends Controller
{

    public function index()
    {
          return view('dashboard');

        
    }
    public function welcome()
    {
        $homeConfig = \App\Models\LandingHomeConfig::first();
        $contactInfo = LandingContactInfo::first();
        $layoutConfig = LandingLayoutConfig::first();
        $heroValues = \App\Models\LandingHeroValue::where('is_active', true)->orderBy('order')->get();
        $testimonials = \App\Models\LandingTestimonial::where('is_active', true)->orderBy('order')->get();

        // Cargar SEO para la página inicio (solo si está activo)
        $page = Page::where('slug', 'home')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.home', compact(
            'homeConfig', 'contactInfo', 'layoutConfig', 'heroValues', 'testimonials', 'seo'
        ));
    }
    public function nosotros()
    {
        $about = LandingAbout::first();
        $layoutConfig = LandingLayoutConfig::first();
        
        // Cargar SEO para la página nosotros (solo si está activo)
        $page = Page::where('slug', 'nosotros')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;
        
        return view('landing_page.nosotros', compact('about', 'layoutConfig', 'seo'));
    }
    
    public function equipo()
    {
        $teamMembers = LandingTeamMember::orderBy('order')->get();
        $layoutConfig = LandingLayoutConfig::first();

        // Cargar SEO para la página equipo (solo si está activo)
        $page = Page::where('slug', 'equipo')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.equipo', compact('teamMembers', 'layoutConfig', 'seo'));
    }

    public function servicios()
    {
        $services = LandingService::published()->orderBy('order')->get();
        $layoutConfig = LandingLayoutConfig::first();

        // Cargar SEO para la página servicios (solo si está activo)
        $page = Page::where('slug', 'servicios')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.servicios', compact('services', 'layoutConfig', 'seo'));
    }

    public function serviceShow(string $slug)
    {
        $service = LandingService::published()->where('slug', $slug)->firstOrFail();
        $layoutConfig = LandingLayoutConfig::first();
        $relatedServices = LandingService::published()
            ->where('id', '!=', $service->id)
            ->orderBy('order')
            ->limit(3)
            ->get();

        // Últimos posts publicados para enlazado interno blog -> servicios
        $relatedPosts = collect();
        if (class_exists(\App\Models\BlogPost::class) && \Illuminate\Support\Facades\Schema::hasTable('blog_posts')) {
            $relatedPosts = \App\Models\BlogPost::published()->latest('published_at')->limit(3)->get();
        }

        // SEO: usar meta del propio servicio; fallback al meta general de /servicios
        $page = Page::where('slug', 'servicios')->first();
        $baseSeo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        $seo = new Seo([
            'meta_title' => $service->meta_title ?: ($service->title . ' | ' . ($layoutConfig->site_title ?? 'Clean Me Adelaide')),
            'meta_description' => $service->meta_description ?: $service->short_description ?: $service->description,
            'meta_keywords' => $service->meta_keywords ?: ($baseSeo->meta_keywords ?? null),
            'canonical_url' => null,
            'robots' => 'index,follow',
            'focus_keyword' => $service->focus_keyword,
            'og_title' => $service->meta_title ?: $service->title,
            'og_description' => $service->meta_description ?: $service->short_description ?: $service->description,
            'og_image_path' => $service->hero_image_path ?: ($baseSeo->og_image_path ?? null),
            'og_type' => 'article',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $service->title,
            'twitter_description' => $service->short_description ?: $service->description,
            'twitter_image_path' => $service->hero_image_path ?: ($baseSeo->og_image_path ?? null),
            'schema_type' => 'Service',
            'schema_data' => [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => $service->title,
                'description' => $service->short_description ?: strip_tags($service->description),
                'provider' => [
                    '@type' => 'LocalBusiness',
                    'name' => $layoutConfig->site_title ?? 'Clean Me Adelaide',
                    'url' => url('/'),
                ],
                'areaServed' => [
                    '@type' => 'City',
                    'name' => 'Adelaide',
                ],
                'url' => route('services.show', $service->slug),
            ],
            'is_active' => true,
        ]);

        return view('landing_page.service_show', compact(
            'service', 'relatedServices', 'relatedPosts', 'layoutConfig', 'seo'
        ));
    }

    public function contacto()
    {
        $contactInfo = LandingContactInfo::first();
        $layoutConfig = LandingLayoutConfig::first();

        // Cargar SEO para la página contacto (solo si está activo)
        $page = Page::where('slug', 'contacto')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.contacto', compact('contactInfo', 'layoutConfig', 'seo'));
    }

    public function servicesCalculator()
    {
        $pricingConfig = LandingPricingConfig::first();
        $pricingRanges = LandingPricingRange::orderBy('order')->get();
        $layoutConfig = LandingLayoutConfig::first();
        $serviceExtras = \App\Models\ServiceExtra::where('is_active', true)->orderBy('order')->get();
        $roomTypePrices = \App\Models\RoomTypePrice::where('is_active', true)->orderBy('order')->get();

        // Simplified pricing: single price per cleaner and per hour
        $cleanerPrice = $pricingConfig->cleaner_price ?? 30;
        $hourPrice = $pricingConfig->hour_price ?? 30;
        $normalMultiplier = $pricingConfig->normal_service_price ?? 0;
        $deepMultiplier = $pricingConfig->deep_service_price ?? 50;
        $bookingTimeSlots = $pricingConfig ? $pricingConfig->getBookingTimeSlots() : (new LandingPricingConfig())->getBookingTimeSlots();

        // Cargar SEO para la página services-calculator (solo si está activo)
        $page = Page::where('slug', 'services-calculator')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.services_calculator', compact(
            'pricingConfig', 'pricingRanges', 'layoutConfig', 'seo',
            'serviceExtras', 'roomTypePrices', 'cleanerPrice', 'hourPrice',
            'normalMultiplier', 'deepMultiplier', 'bookingTimeSlots'
        ));
    }
}

