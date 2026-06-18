<?php

namespace App\Http\Controllers\Catalogo;

use App\Exports\PlantillaProductosExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogo\ProductoRequest;
use App\Models\Pais;
use App\Models\Producto;
use App\Services\Catalogo\ProductoImportService;
use App\Services\Catalogo\ProductoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductoController extends Controller
{
    public function __construct(
        private readonly ProductoService $service,
        private readonly ProductoImportService $importador,
    ) {}

    public function index(Request $request): View
    {
        $query = Producto::orderBy('referencia');

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
     * Descarga una plantilla Excel para importar/actualizar productos. Incluye
     * hasta 3 productos reales del catálogo como ejemplo.
     */
    public function plantillaImportacion(): BinaryFileResponse
    {
        $ejemplos = Producto::orderBy('referencia')
            ->take(3)
            ->get()
            ->map(fn (Producto $p) => [
                $p->referencia,
                $p->descripcion,
                $p->color,
                $p->composicion,
                $p->codigo_pa,
                $p->pais_origen,
                (float) $p->precio_unitario,
                $p->unidad_medida,
                $p->es_prenda ? 'Sí' : 'No',
                $p->activo ? 'Sí' : 'No',
            ])
            ->all();

        return Excel::download(new PlantillaProductosExport($ejemplos), 'plantilla-productos.xlsx');
    }

    /**
     * Procesa un Excel de productos: crea los nuevos y actualiza los existentes
     * por referencia. Las filas con error no detienen el proceso; se devuelven
     * en flash para listarlas en la vista.
     */
    public function importar(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
        ]);

        $resultado = $this->importador->procesar(
            (string) $request->file('archivo')->getRealPath(),
        );

        $mensaje = "Importación finalizada: {$resultado['creados']} creados, {$resultado['actualizados']} actualizados.";
        if ($resultado['errores'] !== []) {
            $mensaje .= ' '.count($resultado['errores']).' fila(s) con error.';
        }

        return redirect()->route('catalogos.productos.index')
            ->with('success', $mensaje)
            ->with('import_errores', $resultado['errores']);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Producto $producto): array
    {
        return [
            'producto' => $producto,
            'paises' => Pais::activos()->orderBy('nombre')->pluck('nombre'),
        ];
    }
}
