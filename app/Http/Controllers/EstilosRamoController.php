<?php

namespace App\Http\Controllers;

use App\Models\EstiloRamo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class EstilosRamoController extends Controller
{
    /**
     * Mostrar listado de estilos de ramo
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = EstiloRamo::query()->orderBy('orden')->orderBy('nombre');

            return DataTables::of($query)
                ->addColumn('imagen_preview', function ($estilo) {
                    if ($estilo->imagen) {
                        return '<img src="' . asset($estilo->imagen) . '" class="img-thumbnail" style="width:60px; height:60px; object-fit:cover;">';
                    }
                    return '<div class="bg-light d-flex align-items-center justify-content-center" style="width:60px; height:60px;"><i class="bi bi-palette text-muted"></i></div>';
                })
                ->addColumn('precio_formateado', function ($estilo) {
                    return '$' . number_format($estilo->precio_base, 0, ',', '.');
                })
                ->addColumn('rango_flores', function ($estilo) {
                    return $estilo->flores_minimo . ' - ' . $estilo->flores_maximo . ' flores';
                })
                ->addColumn('estado', function ($estilo) {
                    return $estilo->activo
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-secondary">Inactivo</span>';
                })
                ->addColumn('action', function ($estilo) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="' . route('estilos-ramo.form', $estilo->id) . '" class="btn btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="' . $estilo->id . '" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['imagen_preview', 'estado', 'action'])
                ->make(true);
        }

        return view('estilos-ramo.index');
    }

    /**
     * Mostrar formulario de creación/edición
     */
    public function form(EstiloRamo $estilo = null)
    {
        return view('estilos-ramo.form', [
            'estilo' => $estilo ?? new EstiloRamo([
                'activo' => true,
                'orden' => 0,
                'flores_minimo' => 5,
                'flores_maximo' => 30,
                'precio_base' => 0
            ])
        ]);
    }

    /**
     * Guardar estilo (crear o actualizar)
     */
    public function guardar(Request $request)
    {
        $estilo = $request->estilo_id ? EstiloRamo::findOrFail($request->estilo_id) : new EstiloRamo();

        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'precio_base' => 'required|numeric|min:0',
            'flores_minimo' => 'required|integer|min:1',
            'flores_maximo' => 'required|integer|min:1|gte:flores_minimo',
            'icono' => 'nullable|string|max:100',
            'color_principal' => 'nullable|string|max:20',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ];

        // Validar imagen solo si se sube una nueva
        if ($request->hasFile('imagen')) {
            $rules['imagen'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        $request->validate($rules);

        // Procesar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($estilo->imagen && file_exists(public_path($estilo->imagen))) {
                unlink(public_path($estilo->imagen));
            }

            $imagen = $request->file('imagen');
            $nombreArchivo = Str::slug($request->nombre) . '-' . time() . '.' . $imagen->getClientOriginalExtension();

            // Guardar en public/images/estilos
            $destino = public_path('images/estilos');
            if (!file_exists($destino)) {
                mkdir($destino, 0755, true);
            }
            $imagen->move($destino, $nombreArchivo);
            $estilo->imagen = 'images/estilos/' . $nombreArchivo;
        }

        // Si se marcó eliminar imagen
        if ($request->has('eliminar_imagen') && $request->eliminar_imagen) {
            if ($estilo->imagen && file_exists(public_path($estilo->imagen))) {
                unlink(public_path($estilo->imagen));
            }
            $estilo->imagen = null;
        }

        $estilo->nombre = $request->nombre;
        $estilo->slug = Str::slug($request->nombre);
        $estilo->descripcion = $request->descripcion;
        $estilo->precio_base = $request->precio_base;
        $estilo->flores_minimo = $request->flores_minimo;
        $estilo->flores_maximo = $request->flores_maximo;
        $estilo->icono = $request->icono;
        $estilo->color_principal = $request->color_principal;
        $estilo->orden = $request->orden ?? 0;
        $estilo->activo = $request->boolean('activo');

        $estilo->save();

        $mensaje = $request->estilo_id ? 'Estilo actualizado correctamente' : 'Estilo creado correctamente';

        return redirect()->route('estilos-ramo.index')->with('success', $mensaje);
    }

    /**
     * Eliminar estilo
     */
    public function eliminar(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:estilos_ramo,id'
        ]);

        $estilo = EstiloRamo::findOrFail($request->id);

        // Eliminar imagen si existe
        if ($estilo->imagen && file_exists(public_path($estilo->imagen))) {
            unlink(public_path($estilo->imagen));
        }

        $estilo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estilo eliminado correctamente'
        ]);
    }

    /**
     * Cambiar estado activo/inactivo (AJAX)
     */
    public function toggleActivo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:estilos_ramo,id'
        ]);

        $estilo = EstiloRamo::findOrFail($request->id);
        $estilo->activo = !$estilo->activo;
        $estilo->save();

        return response()->json([
            'success' => true,
            'message' => $estilo->activo ? 'Estilo activado' : 'Estilo desactivado',
            'activo' => $estilo->activo
        ]);
    }
}
