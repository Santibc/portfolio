<?php

declare(strict_types=1);

namespace App\Http\Controllers\PaginaWeb;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoLandingRequest;
use App\Http\Requests\UpdateProductoLandingRequest;
use App\Models\LandingProducto;
use App\Services\PaginaWebService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Categorías de la sección "Productos" de la landing. */
class ProductoController extends Controller
{
    /** Configuración de las vistas genéricas pagina-web/elementos/*. */
    private const VISTA = [
        'ruta'        => 'pagina-web.productos',
        'titulo'      => 'Productos',
        'singular'    => 'producto',
        'icono'       => 'croissant',
        'descripcion' => 'Categorías de la sección "Lo que horneamos". Cada una tiene su botón "Pedir" por WhatsApp.',
        'campos'      => 'pagina-web.elementos.campos-producto',
        'proporcion'  => 'aspect-[4/5]',
    ];

    public function __construct(private PaginaWebService $svc)
    {
    }

    public function index(): View
    {
        return view('pagina-web.elementos.index', ['vista' => self::VISTA, 'elementos' => LandingProducto::ordenados()->get()]);
    }

    public function create(): View
    {
        return view('pagina-web.elementos.form', ['vista' => self::VISTA, 'elemento' => new LandingProducto(['activo' => true])]);
    }

    public function store(StoreProductoLandingRequest $request): RedirectResponse
    {
        $this->svc->crearElemento(LandingProducto::class, $request->datos(), $request->file('imagen'));

        return redirect()->route('pagina-web.productos.index')->with('success', 'Producto agregado a la página.');
    }

    public function edit(LandingProducto $producto): View
    {
        return view('pagina-web.elementos.form', ['vista' => self::VISTA, 'elemento' => $producto]);
    }

    public function update(UpdateProductoLandingRequest $request, LandingProducto $producto): RedirectResponse
    {
        $this->svc->actualizarElemento($producto, $request->datos(), $request->file('imagen'), $request->boolean('quitar_imagen'));

        return redirect()->route('pagina-web.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(LandingProducto $producto): RedirectResponse
    {
        $this->svc->eliminarElemento($producto);

        return redirect()->route('pagina-web.productos.index')->with('success', 'Producto eliminado de la página.');
    }
}
