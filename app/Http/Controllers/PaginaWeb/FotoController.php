<?php

declare(strict_types=1);

namespace App\Http\Controllers\PaginaWeb;

use App\Enums\FotoLanding;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFotoLandingRequest;
use App\Models\LandingFoto;
use App\Services\PaginaWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Fotos fijas de la landing (portada, historia, local, encargos). */
class FotoController extends Controller
{
    public function __construct(private PaginaWebService $svc)
    {
    }

    public function index(): View
    {
        $fotos = LandingFoto::all()->keyBy(fn (LandingFoto $f) => $f->slot->value);

        // En el orden en que aparecen en la página.
        return view('pagina-web.fotos', [
            'fotos' => collect(FotoLanding::cases())->map(fn (FotoLanding $slot) => $fotos->get($slot->value))->filter(),
        ]);
    }

    public function update(UpdateFotoLandingRequest $request, LandingFoto $foto): RedirectResponse
    {
        $this->svc->actualizarFoto($foto, $request->file('imagen'), $request->validated()['alt'] ?? null);

        return redirect()->route('pagina-web.fotos.index')->with('success', $foto->slot->etiqueta().': guardada.');
    }

    public function destroy(LandingFoto $foto): RedirectResponse
    {
        $this->svc->quitarFoto($foto);

        return redirect()->route('pagina-web.fotos.index')->with('success', $foto->slot->etiqueta().': se quitó la foto.');
    }
}
