<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogo\ProductoRequest;
use App\Models\Impuesto;
use App\Models\Moneda;
use App\Models\Producto;
use App\Services\Catalogo\ProductoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function __construct(private readonly ProductoService $service) {}

    public function index(Request $request): View
    {
        $query = Producto::with(['moneda', 'impuesto'])->orderBy('referencia');

        if ($buscar = $request->string('q')->toString()) {
            $query->where(function ($q) use ($buscar) {
                $q->where('referencia', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('color', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('es_prenda', $request->input('tipo') === 'prenda');
        }

        $productos = $query->paginate(20)->withQueryString();

        return view('catalogo.productos.index', [
            'productos' => $productos,
            'buscar' => $buscar,
            'tipo' => $request->input('tipo', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalogo.productos.form', $this->formData(new Producto));
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('imagen');
        $this->service->crear($data, $request->file('imagen'));

        return redirect()->route('catalogos.productos.index')->with('success', 'Producto creado.');
    }

    public function edit(Producto $producto): View
    {
        return view('catalogo.productos.form', $this->formData($producto));
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $data = $request->safe()->except(['imagen', 'referencia']);
        $this->service->actualizar($producto, $data, $request->file('imagen'));

        return redirect()->route('catalogos.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $this->service->eliminar($producto);

        return redirect()->route('catalogos.productos.index')->with('success', 'Producto eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Producto $producto): array
    {
        return [
            'producto' => $producto,
            'monedas' => Moneda::activas()->orderBy('codigo')->get(),
            'impuestos' => Impuesto::where('activo', true)->orderBy('porcentaje')->get(),
        ];
    }
}
