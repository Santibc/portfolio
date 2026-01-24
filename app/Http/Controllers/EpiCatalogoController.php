<?php

namespace App\Http\Controllers;

use App\Models\EpiCatalogo;
use Illuminate\Http\Request;

class EpiCatalogoController extends Controller
{
    public function index(Request $request)
    {
        $query = EpiCatalogo::withCount('inventario');

        // Filtro por busqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        // Filtro por categoria
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Filtro por requiere revision
        if ($request->filled('requiere_revision')) {
            $query->where('requiere_revision', $request->requiere_revision === '1');
        }

        // Filtro por tiene caducidad
        if ($request->filled('tiene_caducidad')) {
            $query->where('tiene_caducidad', $request->tiene_caducidad === '1');
        }

        $catalogos = $query->orderBy('categoria')->orderBy('nombre')->get();

        // Obtener categorias unicas para el filtro
        $categorias = EpiCatalogo::whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        // Estadisticas
        $stats = [
            'total' => EpiCatalogo::count(),
            'con_caducidad' => EpiCatalogo::where('tiene_caducidad', true)->count(),
            'requieren_revision' => EpiCatalogo::where('requiere_revision', true)->count(),
            'categorias' => EpiCatalogo::whereNotNull('categoria')->distinct()->count('categoria'),
        ];

        return view('epis.catalogo.index', compact('catalogos', 'categorias', 'stats'));
    }

    public function create()
    {
        // Obtener categorias existentes para sugerir
        $categorias = EpiCatalogo::whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        return view('epis.catalogo.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150|unique:epi_catalogo,nombre',
            'categoria' => 'nullable|string|max:100',
            'tiene_caducidad' => 'boolean',
            'requiere_revision' => 'boolean',
            'periodicidad_revision_meses' => 'nullable|integer|min:1|required_if:requiere_revision,true',
        ], [
            'nombre.required' => 'El nombre del EPI es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de EPI con este nombre.',
            'periodicidad_revision_meses.required_if' => 'Debe indicar la periodicidad de revision si el EPI requiere revisiones.',
        ]);

        // Asignar valores por defecto para checkboxes
        $validated['tiene_caducidad'] = $request->boolean('tiene_caducidad', false);
        $validated['requiere_revision'] = $request->boolean('requiere_revision', false);

        // Si no requiere revision, limpiar periodicidad
        if (!$validated['requiere_revision']) {
            $validated['periodicidad_revision_meses'] = null;
        }

        // Convertir strings vacios a null
        if (empty($validated['categoria'])) {
            $validated['categoria'] = null;
        }

        EpiCatalogo::create($validated);

        return redirect()->route('epi-catalogo.index')
            ->with('success', 'Tipo de EPI creado exitosamente.');
    }

    public function edit(EpiCatalogo $epiCatalogo)
    {
        // Obtener categorias existentes para sugerir
        $categorias = EpiCatalogo::whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        // Soportar respuesta JSON para modal AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'epiCatalogo' => $epiCatalogo,
                'categorias' => $categorias,
            ]);
        }

        return view('epis.catalogo.edit', compact('epiCatalogo', 'categorias'));
    }

    public function update(Request $request, EpiCatalogo $epiCatalogo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150|unique:epi_catalogo,nombre,' . $epiCatalogo->id,
            'categoria' => 'nullable|string|max:100',
            'tiene_caducidad' => 'boolean',
            'requiere_revision' => 'boolean',
            'periodicidad_revision_meses' => 'nullable|integer|min:1|required_if:requiere_revision,true',
        ], [
            'nombre.required' => 'El nombre del EPI es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de EPI con este nombre.',
            'periodicidad_revision_meses.required_if' => 'Debe indicar la periodicidad de revision si el EPI requiere revisiones.',
        ]);

        // Asignar valores por defecto para checkboxes
        $validated['tiene_caducidad'] = $request->boolean('tiene_caducidad', false);
        $validated['requiere_revision'] = $request->boolean('requiere_revision', false);

        // Si no requiere revision, limpiar periodicidad
        if (!$validated['requiere_revision']) {
            $validated['periodicidad_revision_meses'] = null;
        }

        // Convertir strings vacios a null
        if (empty($validated['categoria'])) {
            $validated['categoria'] = null;
        }

        $epiCatalogo->update($validated);

        return redirect()->route('epi-catalogo.index')
            ->with('success', 'Tipo de EPI actualizado exitosamente.');
    }

    public function destroy(EpiCatalogo $epiCatalogo)
    {
        // Verificar si tiene inventario asociado
        if ($epiCatalogo->inventario()->count() > 0) {
            return redirect()->route('epi-catalogo.index')
                ->with('error', 'No se puede eliminar este tipo de EPI porque tiene unidades en inventario.');
        }

        $epiCatalogo->delete();

        return redirect()->route('epi-catalogo.index')
            ->with('success', 'Tipo de EPI eliminado exitosamente.');
    }
}
