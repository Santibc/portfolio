<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametros;
use App\Models\LandingConfiguracion;
use App\Models\LandingContactInfo;
use App\Models\LandingAbout;
use App\Models\LandingLayoutConfig;
use App\Models\Page;
use App\Models\Seo;
use App\Models\LandingPricingConfig;
use App\Models\LandingPricingRange;
use App\Models\LandingHeroSection;
use App\Models\LandingHeroValue;
use App\Models\LandingSocialMedia;
use App\Models\LandingHomeAbout;
use App\Models\LandingHomeServices;
use App\Models\LandingServiceItem;
use App\Models\LandingServiceImage;
use App\Models\LandingTestimonial;
use App\Models\LandingAboutValue;

class HomeController extends Controller
{

    public function index()
    {
          return view('dashboard');


    }
    public function welcome()
    {
        $config = LandingConfiguracion::first();
        $contactInfo = LandingContactInfo::first();
        $layoutConfig = LandingLayoutConfig::first();

        // New Landing Content
        $heroSection = LandingHeroSection::first();
        $heroValues = LandingHeroValue::where('is_active', true)->orderBy('order')->get();
        $socialMedia = LandingSocialMedia::first();
        $homeAbout = LandingHomeAbout::first();
        $homeServices = LandingHomeServices::first();
        $serviceItems = LandingServiceItem::where('is_active', true)->orderBy('order')->get();
        $serviceImages = LandingServiceImage::where('is_active', true)->orderBy('order')->get();
        $testimonials = LandingTestimonial::where('is_active', true)->orderBy('order')->get();

        // Cargar SEO para la página inicio (solo si está activo)
        $page = Page::where('slug', 'home')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.home', compact(
            'config', 'contactInfo', 'layoutConfig', 'seo',
            'heroSection', 'heroValues', 'socialMedia', 'homeAbout', 'homeServices',
            'serviceItems', 'serviceImages', 'testimonials'
        ));
    }
    public function nosotros()
    {
        $about = LandingAbout::first();
        $layoutConfig = LandingLayoutConfig::first();
        $aboutValues = LandingAboutValue::where('is_active', true)->orderBy('order')->get();

        // Cargar SEO para la página nosotros (solo si está activo)
        $page = Page::where('slug', 'nosotros')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.nosotros', compact('about', 'layoutConfig', 'seo', 'aboutValues'));
    }

    public function equipo()
    {
        $layoutConfig = LandingLayoutConfig::first();

        // Cargar SEO para la página equipo (solo si está activo)
        $page = Page::where('slug', 'equipo')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.equipo', compact('layoutConfig', 'seo'));
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

        // Cargar SEO para la página services-calculator (solo si está activo)
        $page = Page::where('slug', 'services-calculator')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.services_calculator', compact('pricingConfig', 'pricingRanges', 'layoutConfig', 'seo'));
    }
}
