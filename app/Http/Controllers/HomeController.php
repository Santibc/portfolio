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

        return view('landing_page.home', compact(
            'config', 'carouselImages', 'services', 'steps', 'contactInfo', 'layoutConfig'
        ));
    }
    public function nosotros()
    {
        $about = LandingAbout::first();
        $layoutConfig = LandingLayoutConfig::first();
        
        return view('landing_page.nosotros', compact('about', 'layoutConfig'));
    }
    
    public function equipo()
    {
        $teamMembers = LandingTeamMember::orderBy('order')->get();
        $layoutConfig = LandingLayoutConfig::first();
        
        return view('landing_page.equipo', compact('teamMembers', 'layoutConfig'));
    }
    
    public function contacto()
    {
        $contactInfo = LandingContactInfo::first();
        $layoutConfig = LandingLayoutConfig::first();
        
        return view('landing_page.contacto', compact('contactInfo', 'layoutConfig'));
    }
}
