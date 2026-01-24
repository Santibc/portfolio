<?php

namespace App\Http\Controllers;

use App\Models\FormacionTipo;
use App\Models\TrabajadorFormacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormacionTipoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver_formaciones')->only(['index', 'show']);
        $this->middleware('permission:crear_formaciones')->only(['create', 'store']);
        $this->middleware('permission:editar_formaciones')->only(['edit', 'update']);
        $this->middleware('permission:eliminar_formaciones')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = FormacionTipo::withCount('formaciones');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('obligatoria')) {
            $query->where('obligatoria', $request->obligatoria === '1');
        }

        $tipos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        // Estadísticas
        $stats = [
            'total_tipos' => FormacionTipo::count(),
            'tipos_obligatorios' => FormacionTipo::where('obligatoria', true)->count(),
            'formaciones_registradas' => TrabajadorFormacion::count(),
            'proximas_caducar' => TrabajadorFormacion::whereNotNull('fecha_caducidad')
                ->where('fecha_caducidad', '>', now())
                ->where('fecha_caducidad', '<=', now()->addDays(30))
                ->count(),
            'caducadas' => TrabajadorFormacion::whereNotNull('fecha_caducidad')
                ->where('fecha_caducidad', '<', now())
                ->count(),
        ];

        return view('formacion-tipos.index', compact('tipos', 'stats'));
    }

    public function create()
    {
        return view('formacion-tipos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:formacion_tipos,nombre',
            'descripcion' => 'nullable|string|max:1000',
            'duracion_horas' => 'nullable|integer|min:1|max:999',
            'periodicidad_meses' => 'nullable|integer|min:1|max:240',
            'obligatoria' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de formación con este nombre.',
            'duracion_horas.min' => 'La duración mínima es 1 hora.',
            'periodicidad_meses.min' => 'La periodicidad mínima es 1 mes.',
        ]);

        $validated['obligatoria'] = $request->boolean('obligatoria');

        FormacionTipo::create($validated);

        return redirect()->route('formacion-tipos.index')
            ->with('success', 'Tipo de formación creado exitosamente.');
    }

    public function show(FormacionTipo $formacionTipo)
    {
        $formacionTipo->load(['formaciones.trabajador']);

        // Obtener estadísticas de este tipo de formación
        $stats = [
            'total_trabajadores' => $formacionTipo->formaciones->unique('trabajador_id')->count(),
            'vigentes' => $formacionTipo->formaciones->filter(function ($f) {
                return !$f->fecha_caducidad || $f->fecha_caducidad->isFuture();
            })->count(),
            'caducadas' => $formacionTipo->formaciones->filter(function ($f) {
                return $f->fecha_caducidad && $f->fecha_caducidad->isPast();
            })->count(),
            'proximas_caducar' => $formacionTipo->formaciones->filter(function ($f) {
                return $f->proximo_a_caducar;
            })->count(),
        ];

        return view('formacion-tipos.show', compact('formacionTipo', 'stats'));
    }

    public function edit(FormacionTipo $formacionTipo)
    {
        return view('formacion-tipos.edit', compact('formacionTipo'));
    }

    public function update(Request $request, FormacionTipo $formacionTipo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:formacion_tipos,nombre,' . $formacionTipo->id,
            'descripcion' => 'nullable|string|max:1000',
            'duracion_horas' => 'nullable|integer|min:1|max:999',
            'periodicidad_meses' => 'nullable|integer|min:1|max:240',
            'obligatoria' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de formación con este nombre.',
        ]);

        $validated['obligatoria'] = $request->boolean('obligatoria');

        $formacionTipo->update($validated);

        return redirect()->route('formacion-tipos.index')
            ->with('success', 'Tipo de formación actualizado exitosamente.');
    }

    public function destroy(FormacionTipo $formacionTipo)
    {
        // Verificar si tiene formaciones asociadas
        if ($formacionTipo->formaciones()->exists()) {
            return redirect()->route('formacion-tipos.index')
                ->with('error', 'No se puede eliminar este tipo de formación porque tiene formaciones registradas.');
        }

        $formacionTipo->delete();

        return redirect()->route('formacion-tipos.index')
            ->with('success', 'Tipo de formación eliminado exitosamente.');
    }
}
