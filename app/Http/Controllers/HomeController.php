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
}
