<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Trabajador;
use Illuminate\Http\Request;

class CuadrillaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cuadrilla::with(['capataz', 'trabajadoresActivos']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }

        // Filtro por estado
        if ($request->filled('activa')) {
            $query->where('activa', $request->activa === '1');
        }

        $cuadrillas = $query->orderBy('nombre')->get();

        // Trabajadores disponibles para asignar como capataz
        $trabajadoresDisponibles = Trabajador::where('activo', true)
            ->where('tipo_relacion', 'propio')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        return view('cuadrillas.index', compact('cuadrillas', 'trabajadoresDisponibles'));
    }

    public function create()
    {
        $trabajadoresDisponibles = Trabajador::where('activo', true)
            ->where('tipo_relacion', 'propio')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        return view('cuadrillas.create', compact('trabajadoresDisponibles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:cuadrillas',
            'capataz_id' => 'nullable|exists:trabajadores,id',
            'descripcion' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una cuadrilla con este nombre.',
            'capataz_id.exists' => 'El capataz seleccionado no existe.',
        ]);

        $validated['activa'] = true;

        Cuadrilla::create($validated);

        return redirect()->route('cuadrillas.index')
            ->with('success', 'Cuadrilla creada exitosamente.');
    }

    public function show(Cuadrilla $cuadrilla)
    {
        $cuadrilla->load(['capataz', 'trabajadores', 'obras']);

        // Trabajadores disponibles para añadir (activos y no actualmente en esta cuadrilla)
        $trabajadoresDisponibles = Trabajador::where('activo', true)
            ->whereDoesntHave('cuadrillas', function ($q) use ($cuadrilla) {
                $q->where('cuadrillas.id', $cuadrilla->id)
                  ->where('cuadrilla_trabajadores.activo', true);
            })
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        return view('cuadrillas.show', compact('cuadrilla', 'trabajadoresDisponibles'));
    }

    public function edit(Request $request, Cuadrilla $cuadrilla)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $cuadrilla->id,
                'nombre' => $cuadrilla->nombre,
                'capataz_id' => $cuadrilla->capataz_id,
                'descripcion' => $cuadrilla->descripcion,
                'activa' => $cuadrilla->activa,
            ]);
        }

        $trabajadoresDisponibles = Trabajador::where('activo', true)
            ->where('tipo_relacion', 'propio')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        return view('cuadrillas.edit', compact('cuadrilla', 'trabajadoresDisponibles'));
    }

    public function update(Request $request, Cuadrilla $cuadrilla)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:cuadrillas,nombre,' . $cuadrilla->id,
            'capataz_id' => 'nullable|exists:trabajadores,id',
            'descripcion' => 'nullable|string',
            'activa' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe una cuadrilla con este nombre.',
            'capataz_id.exists' => 'El capataz seleccionado no existe.',
        ]);

        $validated['activa'] = $request->boolean('activa', true);

        $cuadrilla->update($validated);

        return redirect()->route('cuadrillas.index')
            ->with('success', 'Cuadrilla actualizada exitosamente.');
    }

    public function destroy(Cuadrilla $cuadrilla)
    {
        // Verificar que no tiene trabajadores activos
        if ($cuadrilla->trabajadoresActivos()->count() > 0) {
            return redirect()->route('cuadrillas.index')
                ->with('error', 'No se puede eliminar una cuadrilla con trabajadores activos.');
        }

        // Verificar que no tiene obras activas
        if ($cuadrilla->obras()->wherePivot('activo', true)->count() > 0) {
            return redirect()->route('cuadrillas.index')
                ->with('error', 'No se puede eliminar una cuadrilla asignada a obras activas.');
        }

        $cuadrilla->delete();

        return redirect()->route('cuadrillas.index')
            ->with('success', 'Cuadrilla eliminada exitosamente.');
    }

    // =============================================
    // GESTIÓN DE TRABAJADORES EN CUADRILLA
    // =============================================

    public function addTrabajador(Request $request, Cuadrilla $cuadrilla)
    {
        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
        ], [
            'trabajador_id.required' => 'Debe seleccionar un trabajador.',
            'trabajador_id.exists' => 'El trabajador seleccionado no existe.',
        ]);

        // Verificar si ya está en la cuadrilla (activo)
        $existente = $cuadrilla->trabajadores()
            ->where('trabajadores.id', $validated['trabajador_id'])
            ->wherePivot('activo', true)
            ->first();

        if ($existente) {
            return redirect()->route('cuadrillas.show', $cuadrilla)
                ->with('error', 'El trabajador ya está en esta cuadrilla.');
        }

        // Verificar si estuvo antes (reactivar)
        $anterior = $cuadrilla->trabajadores()
            ->where('trabajadores.id', $validated['trabajador_id'])
            ->first();

        if ($anterior) {
            $cuadrilla->trabajadores()->updateExistingPivot($validated['trabajador_id'], [
                'activo' => true,
                'fecha_incorporacion' => now(),
                'fecha_salida' => null,
            ]);
        } else {
            $cuadrilla->trabajadores()->attach($validated['trabajador_id'], [
                'fecha_incorporacion' => now(),
                'activo' => true,
            ]);
        }

        // Desactivar de otras cuadrillas
        $trabajador = Trabajador::find($validated['trabajador_id']);
        $trabajador->cuadrillas()
            ->where('cuadrillas.id', '!=', $cuadrilla->id)
            ->wherePivot('activo', true)
            ->update([
                'cuadrilla_trabajadores.activo' => false,
                'cuadrilla_trabajadores.fecha_salida' => now(),
            ]);

        return redirect()->route('cuadrillas.show', $cuadrilla)
            ->with('success', 'Trabajador añadido a la cuadrilla.');
    }

    public function removeTrabajador(Cuadrilla $cuadrilla, Trabajador $trabajador)
    {
        $cuadrilla->trabajadores()->updateExistingPivot($trabajador->id, [
            'activo' => false,
            'fecha_salida' => now(),
        ]);

        return redirect()->route('cuadrillas.show', $cuadrilla)
            ->with('success', 'Trabajador removido de la cuadrilla.');
    }
}
