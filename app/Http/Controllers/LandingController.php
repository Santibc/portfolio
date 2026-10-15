<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\ContenidoLanding;
use Illuminate\Contracts\View\View;

/** Landing pública de la panadería en "/". El contenido se edita en el módulo "Página web". */
final class LandingController extends Controller
{
    public function __invoke(ContenidoLanding $contenido): View
    {
        return view('landing.index', $contenido->obtener());
    }
}
