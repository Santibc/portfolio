<?php

namespace App\Http\Controllers;

use App\Models\FlorDisponible;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class FloresDisponiblesController extends Controller
{
    /**
     * Mostrar listado de flores disponibles
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = FlorDisponible::query()->orderBy('orden')->orderBy('nombre');

            return DataTables::of($query)
                ->addColumn('imagen_preview', function ($flor) {
                    if ($flor->imagen) {
                        return '<img src="' . asset($flor->imagen) . '" class="img-thumbnail" style="width:60px; height:60px; object-fit:cover;">';
                    }
                    return '<div class="bg-light d-flex align-items-center justify-content-center" style="width:60px; height:60px;"><i class="bi bi-flower1 text-muted"></i></div>';
                })
                ->addColumn('color_badge', function ($flor) {
                    if ($flor->color) {
                        return '<span class="badge" style="background-color: ' . $flor->color . '; width: 25px; height: 25px; display: inline-block; border: 1px solid #ddd;"></span>';
                    }
                    return '-';
                })
                ->addColumn('precio_formateado', function ($flor) {
                    return '$' . number_format($flor->precio_unidad, 0, ',', '.');
                })
                ->addColumn('estado', function ($flor) {
                    if (!$flor->disponible) {
                        return '<span class="badge bg-secondary">Inactivo</span>';
                    }
                    if ($flor->stock <= 0) {
                        return '<span class="badge bg-danger">Sin stock</span>';
                    }
                    if ($flor->stock <= 10) {
                        return '<span class="badge bg-warning">Stock bajo</span>';
                    }
                    return '<span class="badge bg-success">Disponible</span>';
                })
                ->addColumn('action', function ($flor) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="' . route('flores.form', $flor->id) . '" class="btn btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="' . $flor->id . '" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['imagen_preview', 'color_badge', 'estado', 'action'])
                ->make(true);
        }

        return view('flores.index');
    }

    /**
     * Mostrar formulario de creación/edición
     */
    public function form(FlorDisponible $flor = null)
    {
        return view('flores.form', [
            'flor' => $flor ?? new FlorDisponible(['disponible' => true, 'orden' => 0])
        ]);
    }

    /**
     * Guardar flor (crear o actualizar)
     */
    public function guardar(Request $request)
    {
        $flor = $request->flor_id ? FlorDisponible::findOrFail($request->flor_id) : new FlorDisponible();

        $rules = [
            'nombre' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'precio_unidad' => 'required|numeric|min:0',
            'cantidad_minima' => 'required|integer|min:1',
            'cantidad_maxima' => 'required|integer|min:1|gte:cantidad_minima',
            'descripcion' => 'nullable|string|max:500',
            'stock' => 'required|integer|min:0',
            'orden' => 'nullable|integer|min:0',
            'disponible' => 'boolean',
        ];

        // Validar imagen solo si se sube una nueva
        if ($request->hasFile('imagen')) {
            $rules['imagen'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        $request->validate($rules);

        // Procesar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($flor->imagen && file_exists(public_path($flor->imagen))) {
                unlink(public_path($flor->imagen));
            }

            $imagen = $request->file('imagen');
            $nombreArchivo = Str::slug($request->nombre) . '-' . time() . '.' . $imagen->getClientOriginalExtension();

            // Guardar en public/images/flores
            $imagen->move(public_path('images/flores'), $nombreArchivo);
            $flor->imagen = 'images/flores/' . $nombreArchivo;
        }

        // Si se marcó eliminar imagen
        if ($request->has('eliminar_imagen') && $request->eliminar_imagen) {
            if ($flor->imagen && file_exists(public_path($flor->imagen))) {
                unlink(public_path($flor->imagen));
            }
            $flor->imagen = null;
        }

        $flor->nombre = $request->nombre;
        $flor->color = $request->color;
        $flor->precio_unidad = $request->precio_unidad;
        $flor->cantidad_minima = $request->cantidad_minima;
        $flor->cantidad_maxima = $request->cantidad_maxima;
        $flor->descripcion = $request->descripcion;
        $flor->stock = $request->stock;
        $flor->orden = $request->orden ?? 0;
        $flor->disponible = $request->boolean('disponible');

        $flor->save();

        $mensaje = $request->flor_id ? 'Flor actualizada correctamente' : 'Flor creada correctamente';

        return redirect()->route('flores.index')->with('success', $mensaje);
    }

    /**
     * Eliminar flor
     */
    public function eliminar(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:flores_disponibles,id'
        ]);

        $flor = FlorDisponible::findOrFail($request->id);

        // Eliminar imagen si existe
        if ($flor->imagen && file_exists(public_path($flor->imagen))) {
            unlink(public_path($flor->imagen));
        }

        $flor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Flor eliminada correctamente'
        ]);
    }

    /**
     * Actualizar stock rápido (AJAX)
     */
    public function actualizarStock(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:flores_disponibles,id',
            'stock' => 'required|integer|min:0'
        ]);

        $flor = FlorDisponible::findOrFail($request->id);
        $flor->stock = $request->stock;
        $flor->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock actualizado',
            'nuevo_stock' => $flor->stock
        ]);
    }

    /**
     * Cambiar disponibilidad (AJAX)
     */
    public function toggleDisponible(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:flores_disponibles,id'
        ]);

        $flor = FlorDisponible::findOrFail($request->id);
        $flor->disponible = !$flor->disponible;
        $flor->save();

        return response()->json([
            'success' => true,
            'message' => $flor->disponible ? 'Flor activada' : 'Flor desactivada',
            'disponible' => $flor->disponible
        ]);
    }
}
