<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoMercadoRequest;
use App\Http\Requests\UpdateProductoMercadoRequest;
use App\Models\ProductoMercado;
use App\Models\TipoProductoMercado;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ProductoMercadoController extends Controller
{
    public function index(InventarioService $inventario): View
    {
        $productos = ProductoMercado::with('tipo')->latest()->get();
        $stock = $inventario->stockDe($productos->where('controla_inventario', true)->pluck('id')->all());

        $rows = $productos->map(function (ProductoMercado $p) use ($stock) {
            $inventarioHtml = $p->controla_inventario
                ? '<a href="' . route('inventario.show', $p) . '" class="inline-flex items-center gap-1 font-semibold rounded-full text-xs px-2.5 py-1 '
                    . (($stock[$p->id] ?? 0) > 0
                        ? 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200'
                        : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200')
                    . '"><i data-lucide="package" class="w-3 h-3"></i>' . e(ProductoMercado::formatearCantidad($stock[$p->id] ?? 0)) . '</a>'
                : '<span class="text-cream-500 dark:text-cream-500">—</span>';

            $imagen = $p->hasImagen()
                ? '<button type="button" onclick="previewProductoImagen(\'' . e($p->imagen_url) . '\', \'' . e(addslashes($p->nombre)) . '\')" class="block rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/50" title="Ver imagen">'
                    . '<img src="' . e($p->imagen_url) . '" alt="' . e($p->nombre) . '" class="w-10 h-10 rounded-lg object-cover border border-cream-200 dark:border-cream-700 cursor-zoom-in hover:opacity-80 transition-opacity">'
                . '</button>'
                : '<span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-cream-200 text-cream-500 dark:bg-cream-800 dark:text-cream-400"><i data-lucide="image" class="w-4 h-4"></i></span>';

            $activo = $p->activo
                ? '<span class="inline-flex items-center font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 text-xs px-2.5 py-1">Activo</span>'
                : '<span class="inline-flex items-center font-semibold rounded-full bg-cream-200 text-cream-800 dark:bg-cream-800 dark:text-cream-200 text-xs px-2.5 py-1">Inactivo</span>';

            $editUrl = route('productos-mercado.edit', $p);
            $deleteUrl = route('productos-mercado.destroy', $p);
            $csrf = csrf_token();

            $acciones = '<div class="inline-flex items-center gap-2">'
                . '<a href="' . $editUrl . '" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium"><i data-lucide="edit" class="w-3.5 h-3.5"></i>Editar</a>'
                . '<form action="' . $deleteUrl . '" method="POST" class="inline" onsubmit="return confirm(\'¿Eliminar este producto?\');">'
                . '<input type="hidden" name="_token" value="' . $csrf . '">'
                . '<input type="hidden" name="_method" value="DELETE">'
                . '<button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Eliminar</button>'
                . '</form>'
                . '</div>';

            return [
                'imagen'         => $imagen,
                'nombre'         => e($p->nombre),
                'tipo'           => e($p->tipo?->nombre ?? '—'),
                'unidad_empaque' => e($p->unidad_empaque),
                'inventario'     => $inventarioHtml,
                'activo'         => $activo,
                'acciones'       => $acciones,
            ];
        })->values()->all();

        $columns = [
            ['key' => 'imagen',         'label' => 'Imagen',         'sortable' => false],
            ['key' => 'nombre',         'label' => 'Nombre',         'sortable' => true],
            ['key' => 'tipo',           'label' => 'Tipo',           'sortable' => true],
            ['key' => 'unidad_empaque', 'label' => 'Unidad empaque', 'sortable' => true],
            ['key' => 'inventario',     'label' => 'Stock',          'sortable' => false],
            ['key' => 'activo',         'label' => 'Estado',         'sortable' => false],
            ['key' => 'acciones',       'label' => 'Acciones',       'sortable' => false],
        ];

        return view('productos-mercado.index', compact('rows', 'columns'));
    }

    public function create(): View
    {
        $tipos = TipoProductoMercado::orderBy('nombre')->pluck('nombre', 'id');

        return view('productos-mercado.create', compact('tipos'));
    }

    public function store(StoreProductoMercadoRequest $request, InventarioService $inventario): RedirectResponse
    {
        $data = $request->validated();
        $data['activo'] = $request->boolean('activo');
        $data['controla_inventario'] = $request->boolean('controla_inventario');
        $stockInicial = $request->stockInicial();

        $imagen = $request->file('imagen');
        unset($data['imagen'], $data['stock_inicial']);

        // El stock inicial queda en el kardex como un ajuste "Stock inicial".
        $producto = DB::transaction(function () use ($data, $stockInicial, $inventario, $request) {
            $producto = ProductoMercado::create($data);

            if ($stockInicial > 0) {
                $inventario->ajustar($producto, $stockInicial, 'Stock inicial', $request->user()->id);
            }

            return $producto;
        });

        if ($imagen) {
            $this->saveImagen($producto, $imagen);
        }

        return redirect()
            ->route('productos-mercado.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(ProductoMercado $producto, InventarioService $inventario): View
    {
        $tipos = TipoProductoMercado::orderBy('nombre')->pluck('nombre', 'id');
        $stock = $producto->controla_inventario ? $inventario->stockDeProducto($producto->id) : null;

        return view('productos-mercado.edit', compact('producto', 'tipos', 'stock'));
    }

    public function update(UpdateProductoMercadoRequest $request, ProductoMercado $producto): RedirectResponse
    {
        $data = $request->validated();
        $data['activo'] = $request->boolean('activo');
        $data['controla_inventario'] = $request->boolean('controla_inventario');

        $imagen = $request->file('imagen');
        unset($data['imagen']);

        $producto->update($data);

        if ($imagen) {
            $this->saveImagen($producto, $imagen);
        }

        return redirect()
            ->route('productos-mercado.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(ProductoMercado $producto): RedirectResponse
    {
        if ($producto->componentesMenu()->exists()) {
            return redirect()
                ->route('productos-mercado.index')
                ->with('error', 'No se puede eliminar "'.$producto->nombre.'": está vinculado a items del menú de caja.');
        }

        $producto->delete();

        return redirect()
            ->route('productos-mercado.index')
            ->with('success', 'Producto eliminado.');
    }

    private function saveImagen(ProductoMercado $producto, $file): void
    {
        if ($producto->imagen) {
            $oldPath = public_path('uploads/productos-mercado/' . $producto->imagen);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $uploadPath = public_path('uploads/productos-mercado');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $fileName = 'producto_' . $producto->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);

        $producto->imagen = $fileName;
        $producto->save();
    }
}
