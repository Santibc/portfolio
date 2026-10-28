<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\InstaladorCajaService;
use Illuminate\Http\Response;

/** Descarga del instalador que prepara el equipo POS (impresora + acceso directo de la caja). */
class InstaladorCajaController extends Controller
{
    public function __construct(private InstaladorCajaService $instalador) {}

    public function __invoke(): Response
    {
        $script = $this->instalador->generar(route('login'), asset('favicon.ico'));

        return response($script, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="instalar-caja.bat"',
        ]);
    }
}
