<?php

declare(strict_types=1);

namespace App\Http\Controllers\PaginaWeb;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasoLandingRequest;
use App\Http\Requests\UpdatePasoLandingRequest;
use App\Models\LandingPaso;
use App\Services\PaginaWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Pasos de la sección "Del horno a tu mesa". */
class PasoController extends Controller
{
    /** Configuración de las vistas genéricas pagina-web/elementos/*. */
    private const VISTA = [
        'ruta'        => 'pagina-web.proceso',
        'titulo'      => 'Proceso',
        'singular'    => 'paso',
        'icono'       => 'list-ordered',
        'descripcion' => 'Pasos de la sección "Del horno a tu mesa", en el orden en que se muestran.',
        'campos'      => 'pagina-web.elementos.campos-paso',
        'proporcion'  => 'aspect-[3/2]',
    ];

    public function __construct(private PaginaWebService $svc)
    {
    }

    public function index(): View
    {
        return view('pagina-web.elementos.index', ['vista' => self::VISTA, 'elementos' => LandingPaso::ordenados()->get()]);
    }

    public function create(): View
    {
        return view('pagina-web.elementos.form', ['vista' => self::VISTA, 'elemento' => new LandingPaso()]);
    }

    public function store(StorePasoLandingRequest $request): RedirectResponse
    {
        $this->svc->crearElemento(LandingPaso::class, $request->datos(), $request->file('imagen'));

        return redirect()->route('pagina-web.proceso.index')->with('success', 'Paso agregado.');
    }

    public function edit(LandingPaso $paso): View
    {
        return view('pagina-web.elementos.form', ['vista' => self::VISTA, 'elemento' => $paso]);
    }

    public function update(UpdatePasoLandingRequest $request, LandingPaso $paso): RedirectResponse
    {
        $this->svc->actualizarElemento($paso, $request->datos(), $request->file('imagen'), $request->boolean('quitar_imagen'));

        return redirect()->route('pagina-web.proceso.index')->with('success', 'Paso actualizado.');
    }

    public function destroy(LandingPaso $paso): RedirectResponse
    {
        $this->svc->eliminarElemento($paso);

        return redirect()->route('pagina-web.proceso.index')->with('success', 'Paso eliminado.');
    }
}
