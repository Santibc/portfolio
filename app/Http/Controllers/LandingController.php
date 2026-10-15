<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Negocio;
use Illuminate\Contracts\View\View;

/** Landing pública de la panadería en "/". */
final class LandingController extends Controller
{
    public function __invoke(): View
    {
        return view('landing.index', [
            'negocio' => Negocio::desdeConfig(config('panaderia')),
        ]);
    }
}
