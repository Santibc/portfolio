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
        $config = LandingConfiguracion::first();
        $carouselImages = LandingCarouselImage::orderBy('order')->get();
        $services = LandingService::orderBy('order')->get();
        $steps = LandingStep::orderBy('order')->get();
        $contactInfo = LandingContactInfo::first();
        $layoutConfig = LandingLayoutConfig::first();
        
        // Cargar SEO para la página inicio (solo si está activo)
        $page = Page::where('slug', 'home')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.home', compact(
            'config', 'carouselImages', 'services', 'steps', 'contactInfo', 'layoutConfig', 'seo'
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
        $layoutConfig = LandingLayoutConfig::first();

        // Cargar SEO para la página servicios (solo si está activo)
        $page = Page::where('slug', 'servicios')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.servicios', compact('layoutConfig', 'seo'));
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
        $districts = \App\Models\District::active()->get();

        // Cargar SEO para la página services-calculator (solo si está activo)
        $page = Page::where('slug', 'services-calculator')->first();
        $seo = $page && $page->seo && $page->seo->is_active ? $page->seo : null;

        return view('landing_page.services_calculator', compact('pricingConfig', 'pricingRanges', 'layoutConfig', 'seo', 'districts'));
    }
}
