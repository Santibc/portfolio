<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TamanoRamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TamanosRamoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tamanos = TamanoRamo::ordenado()->get();
        return view('admin.tamanos-ramo.index', compact('tamanos'));
    }

    public function create()
    {
        return view('admin.tamanos-ramo.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad_flores_min' => ['required', 'integer', 'min:1'],
            'cantidad_flores_max' => ['required', 'integer', 'min:1', 'gte:cantidad_flores_min'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'imagen' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ], [
            'cantidad_flores_max.gte' => 'La cantidad máxima debe ser mayor o igual a la cantidad mínima',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('ramos/tamanos', 'public');
        }

        $validated['activo'] = $request->has('activo');
        $validated['orden'] = $validated['orden'] ?? 0;
        $validated['precio_base'] = (float) str_replace(',', '.', $validated['precio_base']);

        TamanoRamo::create($validated);

        return redirect()->route('admin.tamanos-ramo.index')
            ->with('success', 'Tamaño de ramo creado exitosamente');
    }

    public function edit(TamanoRamo $tamano_ramo)
    {
        $tamanoRamo = $tamano_ramo;
        return view('admin.tamanos-ramo.form', compact('tamanoRamo'));
    }

    public function update(Request $request, TamanoRamo $tamano_ramo)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'cantidad_flores_min' => ['required', 'integer', 'min:1'],
            'cantidad_flores_max' => ['required', 'integer', 'min:1', 'gte:cantidad_flores_min'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'imagen' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ], [
            'cantidad_flores_max.gte' => 'La cantidad máxima debe ser mayor o igual a la cantidad mínima',
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($tamano_ramo->imagen && Storage::disk('public')->exists($tamano_ramo->imagen)) {
                Storage::disk('public')->delete($tamano_ramo->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('ramos/tamanos', 'public');
        }

        $validated['activo'] = $request->has('activo');
        $validated['orden'] = $validated['orden'] ?? $tamano_ramo->orden;
        $validated['precio_base'] = (float) str_replace(',', '.', $validated['precio_base']);

        $tamano_ramo->update($validated);

        return redirect()->route('admin.tamanos-ramo.index')
            ->with('success', 'Tamaño de ramo actualizado exitosamente');
    }

    public function destroy(TamanoRamo $tamano_ramo)
    {
        try {
            // Verificar si hay ramos personalizados usando este tamaño
            if ($tamano_ramo->ramosPersonalizados()->count() > 0) {
                return redirect()->route('admin.tamanos-ramo.index')
                    ->with('error', 'No se puede eliminar este tamaño porque hay ramos personalizados asociados');
            }

            // Eliminar imagen
            if ($tamano_ramo->imagen && Storage::disk('public')->exists($tamano_ramo->imagen)) {
                Storage::disk('public')->delete($tamano_ramo->imagen);
            }

            $tamano_ramo->delete();

            return redirect()->route('admin.tamanos-ramo.index')
                ->with('success', 'Tamaño de ramo eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('admin.tamanos-ramo.index')
                ->with('error', 'Error al eliminar el tamaño: ' . $e->getMessage());
        }
    }

    public function toggleActivo(TamanoRamo $tamano_ramo)
    {
        $tamano_ramo->update(['activo' => !$tamano_ramo->activo]);

        return response()->json([
            'success' => true,
            'activo' => $tamano_ramo->activo,
            'message' => $tamano_ramo->activo ? 'Tamaño activado' : 'Tamaño desactivado',
        ]);
    }
}
