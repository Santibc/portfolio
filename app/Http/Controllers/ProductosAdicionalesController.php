<?php

namespace App\Http\Controllers;

use App\Models\ProductoAdicional;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductosAdicionalesController extends Controller
{
    /**
     * Mostrar listado de productos adicionales
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductoAdicional::query()->orderBy('orden')->orderBy('nombre');

            return DataTables::of($query)
                ->addColumn('imagen_preview', function ($adicional) {
                    if ($adicional->imagen) {
                        return '<img src="' . asset($adicional->imagen) . '" class="img-thumbnail" style="width:60px; height:60px; object-fit:cover;">';
                    }
                    return '<div class="bg-light d-flex align-items-center justify-content-center" style="width:60px; height:60px;"><i class="bi bi-gift text-muted"></i></div>';
                })
                ->addColumn('categoria_badge', function ($adicional) {
                    $colores = [
                        'bombones' => 'bg-warning text-dark',
                        'peluche' => 'bg-info',
                        'globo' => 'bg-primary',
                        'vino' => 'bg-danger',
                        'licor' => 'bg-dark',
                        'florero' => 'bg-success',
                        'preservante' => 'bg-success',
                        'otro' => 'bg-secondary',
                    ];
                    $color = $colores[$adicional->categoria] ?? 'bg-secondary';
                    return '<span class="badge ' . $color . '">' . $adicional->categoria_nombre . '</span>';
                })
                ->addColumn('tipo_badge', function ($adicional) {
                    if ($adicional->tipo_complementario === ProductoAdicional::TIPO_CROSS_SELLING) {
                        return '<span class="badge bg-primary"><i class="bi bi-shop"></i> Cross-selling</span>';
                    }
                    return '<span class="badge bg-info"><i class="bi bi-tag"></i> Específico</span>';
                })
                ->addColumn('precio_formateado', function ($adicional) {
                    return '$' . number_format($adicional->precio, 0, ',', '.');
                })
                ->addColumn('estado', function ($adicional) {
                    if (!$adicional->disponible) {
                        return '<span class="badge bg-secondary">Inactivo</span>';
                    }
                    if ($adicional->stock <= 0) {
                        return '<span class="badge bg-danger">Sin stock</span>';
                    }
                    if ($adicional->stock <= 5) {
                        return '<span class="badge bg-warning">Stock bajo</span>';
                    }
                    return '<span class="badge bg-success">Disponible</span>';
                })
                ->addColumn('checkout', function ($adicional) {
                    return $adicional->mostrar_en_checkout
                        ? '<i class="bi bi-check-circle-fill text-success"></i>'
                        : '<i class="bi bi-x-circle text-muted"></i>';
                })
                ->addColumn('action', function ($adicional) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="' . route('adicionales.form', $adicional->id) . '" class="btn btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="' . $adicional->id . '" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['imagen_preview', 'categoria_badge', 'tipo_badge', 'estado', 'checkout', 'action'])
                ->make(true);
        }

        return view('adicionales.index');
    }

    /**
     * Mostrar formulario de creación/edición
     */
    public function form(ProductoAdicional $adicional = null)
    {
        return view('adicionales.form', [
            'adicional' => $adicional ?? new ProductoAdicional([
                'disponible' => true,
                'mostrar_en_checkout' => true,
                'orden' => 0
            ]),
            'categorias' => ProductoAdicional::CATEGORIAS
        ]);
    }

    /**
     * Guardar producto adicional (crear o actualizar)
     */
    public function guardar(Request $request)
    {
        $adicional = $request->adicional_id
            ? ProductoAdicional::findOrFail($request->adicional_id)
            : new ProductoAdicional();

        $rules = [
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|in:' . implode(',', array_keys(ProductoAdicional::CATEGORIAS)),
            'tipo_complementario' => 'required|in:cross_selling,especifico',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
            'stock' => 'required|integer|min:0',
            'orden' => 'nullable|integer|min:0',
            'disponible' => 'boolean',
            'mostrar_en_checkout' => 'boolean',
        ];

        if ($request->hasFile('imagen')) {
            $rules['imagen'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        $request->validate($rules);

        // Procesar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($adicional->imagen && file_exists(public_path($adicional->imagen))) {
                unlink(public_path($adicional->imagen));
            }

            $imagen = $request->file('imagen');
            $nombreArchivo = Str::slug($request->nombre) . '-' . time() . '.' . $imagen->getClientOriginalExtension();

            // Crear directorio si no existe
            $directorio = public_path('images/adicionales');
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }

            $imagen->move($directorio, $nombreArchivo);
            $adicional->imagen = 'images/adicionales/' . $nombreArchivo;
        }

        // Si se marcó eliminar imagen
        if ($request->has('eliminar_imagen') && $request->eliminar_imagen) {
            if ($adicional->imagen && file_exists(public_path($adicional->imagen))) {
                unlink(public_path($adicional->imagen));
            }
            $adicional->imagen = null;
        }

        $adicional->nombre = $request->nombre;
        $adicional->categoria = $request->categoria;
        $adicional->tipo_complementario = $request->tipo_complementario;
        $adicional->precio = $request->precio;
        $adicional->descripcion = $request->descripcion;
        $adicional->stock = $request->stock;
        $adicional->orden = $request->orden ?? 0;
        $adicional->disponible = $request->boolean('disponible');
        $adicional->mostrar_en_checkout = $request->boolean('mostrar_en_checkout');

        $adicional->save();

        $mensaje = $request->adicional_id ? 'Producto adicional actualizado' : 'Producto adicional creado';

        return redirect()->route('adicionales.index')->with('success', $mensaje);
    }

    /**
     * Eliminar producto adicional
     */
    public function eliminar(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:productos_adicionales,id'
        ]);

        $adicional = ProductoAdicional::findOrFail($request->id);

        if ($adicional->imagen && file_exists(public_path($adicional->imagen))) {
            unlink(public_path($adicional->imagen));
        }

        $adicional->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto adicional eliminado'
        ]);
    }

    /**
     * Toggle disponibilidad (AJAX)
     */
    public function toggleDisponible(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:productos_adicionales,id'
        ]);

        $adicional = ProductoAdicional::findOrFail($request->id);
        $adicional->disponible = !$adicional->disponible;
        $adicional->save();

        return response()->json([
            'success' => true,
            'message' => $adicional->disponible ? 'Producto activado' : 'Producto desactivado',
            'disponible' => $adicional->disponible
        ]);
    }
}
