<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametros;

class HomeController extends Controller
{

    public function index()
    {
          return view('dashboard');

        
    }
    public function welcome()
    {
          return view('landing_page.home');

        
    }
    public function nosotros()
    {
          return view('landing_page.nosotros');

        
    }
    public function equipo()
    {
          return view('landing_page.equipo');

        
    }
    public function contacto()
    {
          return view('landing_page.contacto');

        
    }
}
