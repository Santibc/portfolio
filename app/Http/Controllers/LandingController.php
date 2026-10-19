<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\LandingService;
use Illuminate\Contracts\View\View;

/** Landing pública de Dorilokos Mix (página de inicio). */
class LandingController extends Controller
{
    public function __invoke(LandingService $landing): View
    {
        return view('landing.index', $landing->vista());
    }
}
