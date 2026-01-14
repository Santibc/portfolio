<?php

namespace App\Http\Controllers;

use App\Models\EnvolturaRamo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class EnvolturasRamoController extends Controller
{
    /**
     * Mostrar listado de envolturas de ramo
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = EnvolturaRamo::query()->orderBy('orden')->orderBy('nombre');

            return DataTables::of($query)
                ->addColumn('imagen_preview', function ($envoltura) {
                    if ($envoltura->imagen) {
                        return '<img src="' . asset($envoltura->imagen) . '" class="img-thumbnail" style="width:60px; height:60px; object-fit:cover;">';
                    }
                    return '<div class="bg-light d-flex align-items-center justify-content-center" style="width:60px; height:60px;"><i class="bi bi-box text-muted"></i></div>';
                })
                ->addColumn('precio_formateado', function ($envoltura) {
                    if ($envoltura->precio_adicional > 0) {
                        return '+$' . number_format($envoltura->precio_adicional, 0, ',', '.');
                    }
                    return '<span class="text-success">Incluido</span>';
                })
                ->addColumn('estado', function ($envoltura) {
                    return $envoltura->activo
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-secondary">Inactivo</span>';
                })
                ->addColumn('action', function ($envoltura) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="' . route('envolturas-ramo.form', $envoltura->id) . '" class="btn btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="' . $envoltura->id . '" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['imagen_preview', 'precio_formateado', 'estado', 'action'])
                ->make(true);
        }

        return view('envolturas-ramo.index');
    }

    /**
     * Mostrar formulario de creación/edición
     */
    public function form(EnvolturaRamo $envoltura = null)
    {
        return view('envolturas-ramo.form', [
            'envoltura' => $envoltura ?? new EnvolturaRamo([
                'activo' => true,
                'orden' => 0,
                'precio_adicional' => 0
            ])
        ]);
    }

    /**
     * Guardar envoltura (crear o actualizar)
     */
    public function guardar(Request $request)
    {
        $envoltura = $request->envoltura_id ? EnvolturaRamo::findOrFail($request->envoltura_id) : new EnvolturaRamo();

        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'precio_adicional' => 'required|numeric|min:0',
            'icono' => 'nullable|string|max:100',
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
            if ($envoltura->imagen && file_exists(public_path($envoltura->imagen))) {
                unlink(public_path($envoltura->imagen));
            }

            $imagen = $request->file('imagen');
            $nombreArchivo = Str::slug($request->nombre) . '-' . time() . '.' . $imagen->getClientOriginalExtension();

            // Guardar en public/images/envolturas
            $destino = public_path('images/envolturas');
            if (!file_exists($destino)) {
                mkdir($destino, 0755, true);
            }
            $imagen->move($destino, $nombreArchivo);
            $envoltura->imagen = 'images/envolturas/' . $nombreArchivo;
        }

        // Si se marcó eliminar imagen
        if ($request->has('eliminar_imagen') && $request->eliminar_imagen) {
            if ($envoltura->imagen && file_exists(public_path($envoltura->imagen))) {
                unlink(public_path($envoltura->imagen));
            }
            $envoltura->imagen = null;
        }

        $envoltura->nombre = $request->nombre;
        $envoltura->slug = Str::slug($request->nombre);
        $envoltura->descripcion = $request->descripcion;
        $envoltura->precio_adicional = $request->precio_adicional;
        $envoltura->icono = $request->icono;
        $envoltura->orden = $request->orden ?? 0;
        $envoltura->activo = $request->boolean('activo');

        $envoltura->save();

        $mensaje = $request->envoltura_id ? 'Envoltura actualizada correctamente' : 'Envoltura creada correctamente';

        return redirect()->route('envolturas-ramo.index')->with('success', $mensaje);
    }

    /**
     * Eliminar envoltura
     */
    public function eliminar(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:envolturas_ramo,id'
        ]);

        $envoltura = EnvolturaRamo::findOrFail($request->id);

        // Eliminar imagen si existe
        if ($envoltura->imagen && file_exists(public_path($envoltura->imagen))) {
            unlink(public_path($envoltura->imagen));
        }

        $envoltura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Envoltura eliminada correctamente'
        ]);
    }

    /**
     * Cambiar estado activo/inactivo (AJAX)
     */
    public function toggleActivo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:envolturas_ramo,id'
        ]);

        $envoltura = EnvolturaRamo::findOrFail($request->id);
        $envoltura->activo = !$envoltura->activo;
        $envoltura->save();

        return response()->json([
            'success' => true,
            'message' => $envoltura->activo ? 'Envoltura activada' : 'Envoltura desactivada',
            'activo' => $envoltura->activo
        ]);
    }
}
