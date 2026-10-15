<?php

declare(strict_types=1);

namespace App\Http\Controllers\PaginaWeb;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGaleriaLandingRequest;
use App\Http\Requests\UpdateGaleriaLandingRequest;
use App\Models\LandingGaleriaFoto;
use App\Services\PaginaWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Galería de fotos de la landing. */
class GaleriaController extends Controller
{
    /** Configuración de las vistas genéricas pagina-web/elementos/*. */
    private const VISTA = [
        'ruta'        => 'pagina-web.galeria',
        'titulo'      => 'Galería',
        'singular'    => 'foto',
        'icono'       => 'images',
        'descripcion' => 'Fotos de la sección "Galería". Una foto sin imagen se muestra como espacio reservado ("Próximamente").',
        'campos'      => 'pagina-web.elementos.campos-galeria',
        'proporcion'  => 'aspect-[4/3]',
    ];

    public function __construct(private PaginaWebService $svc)
    {
    }

    public function index(): View
    {
        return view('pagina-web.elementos.index', ['vista' => self::VISTA, 'elementos' => LandingGaleriaFoto::ordenados()->get()]);
    }

    public function create(): View
    {
        return view('pagina-web.elementos.form', ['vista' => self::VISTA, 'elemento' => new LandingGaleriaFoto()]);
    }

    public function store(StoreGaleriaLandingRequest $request): RedirectResponse
    {
        $this->svc->crearElemento(LandingGaleriaFoto::class, $request->datos(), $request->file('imagen'));

        return redirect()->route('pagina-web.galeria.index')->with('success', 'Foto agregada a la galería.');
    }

    public function edit(LandingGaleriaFoto $foto): View
    {
        return view('pagina-web.elementos.form', ['vista' => self::VISTA, 'elemento' => $foto]);
    }

    public function update(UpdateGaleriaLandingRequest $request, LandingGaleriaFoto $foto): RedirectResponse
    {
        $this->svc->actualizarElemento($foto, $request->datos(), $request->file('imagen'), $request->boolean('quitar_imagen'));

        return redirect()->route('pagina-web.galeria.index')->with('success', 'Foto de la galería actualizada.');
    }

    public function destroy(LandingGaleriaFoto $foto): RedirectResponse
    {
        $this->svc->eliminarElemento($foto);

        return redirect()->route('pagina-web.galeria.index')->with('success', 'Foto eliminada de la galería.');
    }
}
