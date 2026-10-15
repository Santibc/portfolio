<?php

declare(strict_types=1);

namespace App\Http\Controllers\PaginaWeb;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNegocioLandingRequest;
use App\Models\LandingAjuste;
use App\Models\LandingHorario;
use App\Services\PaginaWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Datos del negocio, contacto, redes y horario de la landing. */
class NegocioController extends Controller
{
    public function __construct(private PaginaWebService $svc)
    {
    }

    public function edit(): View
    {
        return view('pagina-web.negocio', [
            'ajuste' => LandingAjuste::actual(),
            'horarios' => LandingHorario::ordenados()->get(['dias', 'horas'])->toArray(),
        ]);
    }

    public function update(UpdateNegocioLandingRequest $request): RedirectResponse
    {
        $this->svc->actualizarNegocio($request->datosNegocio(), $request->horarios(), (int) $request->user()->id);

        return redirect()->route('pagina-web.negocio.edit')->with('success', 'Datos del negocio actualizados.');
    }
}
