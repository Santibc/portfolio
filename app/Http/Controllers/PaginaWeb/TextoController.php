<?php

declare(strict_types=1);

namespace App\Http\Controllers\PaginaWeb;

use App\Data\TextosLanding;
use App\Enums\IconoLanding;
use App\Enums\TextoLanding;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTextosLandingRequest;
use App\Models\LandingCifra;
use App\Models\LandingTexto;
use App\Models\LandingValor;
use App\Services\PaginaWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Textos de la landing, cifras de "Nuestra historia" y tarjetas de "Por qué elegirnos". */
class TextoController extends Controller
{
    public function __construct(private PaginaWebService $svc)
    {
    }

    public function edit(): View
    {
        return view('pagina-web.textos', [
            'secciones' => TextoLanding::porSeccion(),
            'textos' => new TextosLanding(LandingTexto::query()->pluck('valor', 'clave')->all()),
            'cifras' => LandingCifra::ordenados()->get(['valor', 'prefijo', 'sufijo', 'texto'])->toArray(),
            'valores' => LandingValor::ordenados()->get()
                ->map(fn (LandingValor $v) => ['icono' => $v->icono->value, 'titulo' => $v->titulo, 'texto' => $v->texto])
                ->all(),
            'iconos' => IconoLanding::opciones(),
        ]);
    }

    public function update(UpdateTextosLandingRequest $request): RedirectResponse
    {
        $this->svc->actualizarTextos($request->textos(), $request->cifras(), $request->valores(), (int) $request->user()->id);

        return redirect()->route('pagina-web.textos.edit')->with('success', 'Textos de la página actualizados.');
    }
}
