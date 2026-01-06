<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarruselEmpresa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CarruselController extends Controller
{
    public function index()
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return redirect()->route('empresa.crear')
                ->with('warning', 'Debe crear su empresa antes de gestionar el carrusel.');
        }

        $imagenes = CarruselEmpresa::where('empresa_id', $empresa->id)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        return view('carrusel.index', compact('imagenes', 'empresa'));
    }

    public function create()
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return redirect()->route('empresa.crear')
                ->with('warning', 'Debe crear su empresa antes de gestionar el carrusel.');
        }

        return view('carrusel.form', [
            'imagen' => null,
            'empresa' => $empresa
        ]);
    }

    public function store(Request $request)
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return redirect()->route('empresa.crear')
                ->with('warning', 'Debe crear su empresa antes de gestionar el carrusel.');
        }

        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'titulo' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $data = $request->only(['titulo', 'descripcion', 'link', 'orden', 'fecha_inicio', 'fecha_fin']);
        $data['empresa_id'] = $empresa->id;
        $data['activo'] = $request->boolean('activo', true);
        $data['orden'] = $request->orden ?? 0;

        // Guardar imagen
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = 'carrusel_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'images/carrusel/' . $empresa->id;

            // Crear directorio si no existe
            $fullPath = public_path($path);
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }

            $file->move($fullPath, $filename);
            $data['imagen'] = $path . '/' . $filename;
        }

        CarruselEmpresa::create($data);

        return redirect()->route('carrusel.index')
            ->with('success', 'Imagen agregada al carrusel exitosamente.');
    }

    public function edit($id)
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return redirect()->route('empresa.crear')
                ->with('warning', 'Debe crear su empresa antes de gestionar el carrusel.');
        }

        $imagen = CarruselEmpresa::where('empresa_id', $empresa->id)
            ->findOrFail($id);

        return view('carrusel.form', compact('imagen', 'empresa'));
    }

    public function update(Request $request, $id)
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return redirect()->route('empresa.crear')
                ->with('warning', 'Debe crear su empresa antes de gestionar el carrusel.');
        }

        $imagen = CarruselEmpresa::where('empresa_id', $empresa->id)
            ->findOrFail($id);

        $request->validate([
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'titulo' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $data = $request->only(['titulo', 'descripcion', 'link', 'orden', 'fecha_inicio', 'fecha_fin']);
        $data['activo'] = $request->boolean('activo', true);
        $data['orden'] = $request->orden ?? 0;

        // Actualizar imagen si se sube una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($imagen->imagen && File::exists(public_path($imagen->imagen))) {
                File::delete(public_path($imagen->imagen));
            }

            $file = $request->file('imagen');
            $filename = 'carrusel_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'images/carrusel/' . $empresa->id;

            $fullPath = public_path($path);
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }

            $file->move($fullPath, $filename);
            $data['imagen'] = $path . '/' . $filename;
        }

        $imagen->update($data);

        return redirect()->route('carrusel.index')
            ->with('success', 'Imagen del carrusel actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return response()->json(['error' => 'Empresa no encontrada'], 404);
        }

        $imagen = CarruselEmpresa::where('empresa_id', $empresa->id)
            ->findOrFail($id);

        // Eliminar archivo de imagen
        if ($imagen->imagen && File::exists(public_path($imagen->imagen))) {
            File::delete(public_path($imagen->imagen));
        }

        $imagen->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Imagen eliminada exitosamente.']);
        }

        return redirect()->route('carrusel.index')
            ->with('success', 'Imagen eliminada exitosamente.');
    }

    public function toggleActivo($id)
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return response()->json(['error' => 'Empresa no encontrada'], 404);
        }

        $imagen = CarruselEmpresa::where('empresa_id', $empresa->id)
            ->findOrFail($id);

        $imagen->update(['activo' => !$imagen->activo]);

        return response()->json([
            'success' => true,
            'activo' => $imagen->activo,
            'message' => $imagen->activo ? 'Imagen activada' : 'Imagen desactivada'
        ]);
    }

    public function updateOrden(Request $request)
    {
        $empresa = auth()->user()->empresa;
        if (!$empresa) {
            return response()->json(['error' => 'Empresa no encontrada'], 404);
        }

        $request->validate([
            'orden' => 'required|array',
            'orden.*' => 'integer'
        ]);

        foreach ($request->orden as $id => $orden) {
            CarruselEmpresa::where('empresa_id', $empresa->id)
                ->where('id', $id)
                ->update(['orden' => $orden]);
        }

        return response()->json(['success' => true, 'message' => 'Orden actualizado correctamente.']);
    }
}
