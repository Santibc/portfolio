<?php

namespace App\Http\Controllers;

use App\Models\ParteDiario;
use App\Models\ParteDiarioTrabajador;
use App\Models\Obra;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParteDiarioController extends Controller
{
    /**
     * Display a listing of partes diarios.
     */
    public function index(Request $request)
    {
        $query = ParteDiario::with(['obra', 'creadoPor']);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('jornada')) {
            $query->where('jornada', $request->jornada);
        }

        // Por defecto mostrar el mes actual
        if (!$request->filled('fecha_desde') && !$request->filled('fecha_hasta')) {
            $query->where('fecha', '>=', now()->startOfMonth())
                  ->where('fecha', '<=', now()->endOfMonth());
        }

        $partes = $query->orderBy('fecha', 'desc')
                        ->paginate(25);

        // Estadísticas del período
        $stats = [
            'total_partes' => $query->count(),
            'borradores' => ParteDiario::borradores()->count(),
            'pendientes_validar' => ParteDiario::completados()->count(),
            'desbroce_total' => ParteDiario::where('fecha', '>=', now()->startOfMonth())
                                           ->sum('desbroce_total_m2'),
        ];

        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])
                     ->orderBy('nombre')
                     ->get();

        return view('partes-diarios.index', compact('partes', 'obras', 'stats'));
    }

    /**
     * Show the form for creating a new parte diario.
     */
    public function create(Request $request)
    {
        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])
                     ->with('cliente')
                     ->orderBy('nombre')
                     ->get();

        $trabajadores = Trabajador::where('activo', true)
                                   ->orderBy('nombre')
                                   ->get();

        // Pre-seleccionar obra si viene en el request
        $obraSeleccionada = null;
        if ($request->filled('obra_id')) {
            $obraSeleccionada = Obra::find($request->obra_id);
        }

        return view('partes-diarios.create', compact('obras', 'trabajadores', 'obraSeleccionada'));
    }

    /**
     * Store a newly created parte diario.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'fecha' => 'required|date',
            'jornada' => 'required|in:diurna,nocturna',
            'linea' => 'nullable|string|max:100',
            'trayecto' => 'nullable|string|max:255',
            'gerencia_jefatura' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:100',
            'brigada' => 'nullable|string|max:100',
            'desbroce_total_m2' => 'nullable|numeric|min:0',
            'desbroce_p5_m2' => 'nullable|numeric|min:0',
            'desbroce_p6_m2' => 'nullable|numeric|min:0',
            'limpieza_p8_m2' => 'nullable|numeric|min:0',
            'herbicida_p4_m2' => 'nullable|numeric|min:0',
            'talas_unidades' => 'nullable|integer|min:0',
            'podas_unidades' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
            'incidencias' => 'nullable|string',
            'trabajadores' => 'nullable|array',
            'trabajadores.*' => 'exists:trabajadores,id',
        ]);

        // Verificar que no exista un parte para la misma obra y fecha
        $existe = ParteDiario::where('obra_id', $validated['obra_id'])
                             ->where('fecha', $validated['fecha'])
                             ->exists();

        if ($existe) {
            return back()->withErrors(['fecha' => 'Ya existe un parte diario para esta obra en esta fecha.'])
                         ->withInput();
        }

        DB::beginTransaction();
        try {
            $parte = ParteDiario::create([
                'obra_id' => $validated['obra_id'],
                'fecha' => $validated['fecha'],
                'jornada' => $validated['jornada'],
                'linea' => $validated['linea'] ?? null,
                'trayecto' => $validated['trayecto'] ?? null,
                'gerencia_jefatura' => $validated['gerencia_jefatura'] ?? null,
                'distrito' => $validated['distrito'] ?? null,
                'brigada' => $validated['brigada'] ?? 'MANZER',
                'desbroce_total_m2' => $validated['desbroce_total_m2'] ?? 0,
                'desbroce_p5_m2' => $validated['desbroce_p5_m2'] ?? 0,
                'desbroce_p6_m2' => $validated['desbroce_p6_m2'] ?? 0,
                'limpieza_p8_m2' => $validated['limpieza_p8_m2'] ?? 0,
                'herbicida_p4_m2' => $validated['herbicida_p4_m2'] ?? 0,
                'talas_unidades' => $validated['talas_unidades'] ?? 0,
                'podas_unidades' => $validated['podas_unidades'] ?? 0,
                'observaciones' => $validated['observaciones'] ?? null,
                'incidencias' => $validated['incidencias'] ?? null,
                'estado' => 'borrador',
                'creado_por' => Auth::id(),
            ]);

            // Agregar trabajadores
            if (!empty($validated['trabajadores'])) {
                foreach ($validated['trabajadores'] as $trabajadorId) {
                    ParteDiarioTrabajador::create([
                        'parte_diario_id' => $parte->id,
                        'trabajador_id' => $trabajadorId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('partes-diarios.show', $parte)
                             ->with('success', 'Parte diario creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el parte: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified parte diario.
     */
    public function show(ParteDiario $partes_diario)
    {
        $partes_diario->load([
            'obra.cliente',
            'creadoPor',
            'trabajadores.trabajador',
            'lineas',
            'herbicidas',
        ]);

        return view('partes-diarios.show', compact('partes_diario'));
    }

    /**
     * Show the form for editing the specified parte diario.
     */
    public function edit(ParteDiario $partes_diario)
    {
        if ($partes_diario->estado === 'validado') {
            return back()->with('error', 'No se puede editar un parte validado.');
        }

        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])
                     ->orderBy('nombre')
                     ->get();

        $trabajadores = Trabajador::where('activo', true)
                                   ->orderBy('nombre')
                                   ->get();

        $partes_diario->load('trabajadores');

        return view('partes-diarios.edit', compact('partes_diario', 'obras', 'trabajadores'));
    }

    /**
     * Update the specified parte diario.
     */
    public function update(Request $request, ParteDiario $partes_diario)
    {
        if ($partes_diario->estado === 'validado') {
            return back()->with('error', 'No se puede editar un parte validado.');
        }

        $validated = $request->validate([
            'jornada' => 'required|in:diurna,nocturna',
            'linea' => 'nullable|string|max:100',
            'trayecto' => 'nullable|string|max:255',
            'gerencia_jefatura' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:100',
            'brigada' => 'nullable|string|max:100',
            'desbroce_total_m2' => 'nullable|numeric|min:0',
            'desbroce_p5_m2' => 'nullable|numeric|min:0',
            'desbroce_p6_m2' => 'nullable|numeric|min:0',
            'limpieza_p8_m2' => 'nullable|numeric|min:0',
            'herbicida_p4_m2' => 'nullable|numeric|min:0',
            'talas_unidades' => 'nullable|integer|min:0',
            'podas_unidades' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
            'incidencias' => 'nullable|string',
            'trabajadores' => 'nullable|array',
            'trabajadores.*' => 'exists:trabajadores,id',
        ]);

        DB::beginTransaction();
        try {
            $partes_diario->update([
                'jornada' => $validated['jornada'],
                'linea' => $validated['linea'] ?? null,
                'trayecto' => $validated['trayecto'] ?? null,
                'gerencia_jefatura' => $validated['gerencia_jefatura'] ?? null,
                'distrito' => $validated['distrito'] ?? null,
                'brigada' => $validated['brigada'] ?? 'MANZER',
                'desbroce_total_m2' => $validated['desbroce_total_m2'] ?? 0,
                'desbroce_p5_m2' => $validated['desbroce_p5_m2'] ?? 0,
                'desbroce_p6_m2' => $validated['desbroce_p6_m2'] ?? 0,
                'limpieza_p8_m2' => $validated['limpieza_p8_m2'] ?? 0,
                'herbicida_p4_m2' => $validated['herbicida_p4_m2'] ?? 0,
                'talas_unidades' => $validated['talas_unidades'] ?? 0,
                'podas_unidades' => $validated['podas_unidades'] ?? 0,
                'observaciones' => $validated['observaciones'] ?? null,
                'incidencias' => $validated['incidencias'] ?? null,
            ]);

            // Actualizar trabajadores
            $partes_diario->trabajadores()->delete();
            if (!empty($validated['trabajadores'])) {
                foreach ($validated['trabajadores'] as $trabajadorId) {
                    ParteDiarioTrabajador::create([
                        'parte_diario_id' => $partes_diario->id,
                        'trabajador_id' => $trabajadorId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('partes-diarios.show', $partes_diario)
                             ->with('success', 'Parte diario actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el parte: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Remove the specified parte diario.
     */
    public function destroy(ParteDiario $partes_diario)
    {
        if ($partes_diario->estado === 'validado') {
            return back()->with('error', 'No se puede eliminar un parte validado.');
        }

        $partes_diario->delete();

        return redirect()->route('partes-diarios.index')
                         ->with('success', 'Parte diario eliminado correctamente.');
    }

    /**
     * Completar un parte diario (cambiar de borrador a completado).
     */
    public function completar(ParteDiario $partes_diario)
    {
        if ($partes_diario->estado !== 'borrador') {
            return back()->with('error', 'Este parte ya está completado o validado.');
        }

        $partes_diario->update(['estado' => 'completado']);

        return back()->with('success', 'Parte marcado como completado.');
    }

    /**
     * Validar un parte diario.
     */
    public function validar(ParteDiario $partes_diario)
    {
        if ($partes_diario->estado === 'validado') {
            return back()->with('error', 'Este parte ya está validado.');
        }

        $partes_diario->update(['estado' => 'validado']);

        return back()->with('success', 'Parte validado correctamente.');
    }

    /**
     * Agregar trabajador al parte.
     */
    public function addTrabajador(Request $request, ParteDiario $partes_diario)
    {
        if ($partes_diario->estado === 'validado') {
            return back()->with('error', 'No se puede modificar un parte validado.');
        }

        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'es_aplicador' => 'nullable|boolean',
            'dni_aplicador' => 'nullable|string|max:20',
            'horas_trabajadas' => 'nullable|numeric|min:0|max:24',
        ]);

        // Verificar que no esté ya asignado
        $existe = $partes_diario->trabajadores()
                                ->where('trabajador_id', $validated['trabajador_id'])
                                ->exists();

        if ($existe) {
            return back()->with('error', 'El trabajador ya está asignado a este parte.');
        }

        ParteDiarioTrabajador::create([
            'parte_diario_id' => $partes_diario->id,
            'trabajador_id' => $validated['trabajador_id'],
            'es_aplicador' => $validated['es_aplicador'] ?? false,
            'dni_aplicador' => $validated['dni_aplicador'] ?? null,
            'horas_trabajadas' => $validated['horas_trabajadas'] ?? null,
        ]);

        return back()->with('success', 'Trabajador agregado correctamente.');
    }

    /**
     * Eliminar trabajador del parte.
     */
    public function removeTrabajador(ParteDiario $partes_diario, $trabajadorId)
    {
        if ($partes_diario->estado === 'validado') {
            return back()->with('error', 'No se puede modificar un parte validado.');
        }

        $partes_diario->trabajadores()
                      ->where('trabajador_id', $trabajadorId)
                      ->delete();

        return back()->with('success', 'Trabajador eliminado del parte.');
    }

    /**
     * Duplicar un parte para otra fecha.
     */
    public function duplicar(Request $request, ParteDiario $partes_diario)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
        ]);

        // Verificar que no exista un parte para la misma obra y fecha
        $existe = ParteDiario::where('obra_id', $partes_diario->obra_id)
                             ->where('fecha', $validated['fecha'])
                             ->exists();

        if ($existe) {
            return back()->withErrors(['fecha' => 'Ya existe un parte para esta obra en la fecha seleccionada.']);
        }

        DB::beginTransaction();
        try {
            $nuevoParte = $partes_diario->replicate();
            $nuevoParte->fecha = $validated['fecha'];
            $nuevoParte->estado = 'borrador';
            $nuevoParte->encargado_firma = null;
            $nuevoParte->cliente_firma = null;
            $nuevoParte->creado_por = Auth::id();
            $nuevoParte->save();

            // Duplicar trabajadores
            foreach ($partes_diario->trabajadores as $trabajador) {
                ParteDiarioTrabajador::create([
                    'parte_diario_id' => $nuevoParte->id,
                    'trabajador_id' => $trabajador->trabajador_id,
                    'es_aplicador' => $trabajador->es_aplicador,
                    'dni_aplicador' => $trabajador->dni_aplicador,
                ]);
            }

            DB::commit();

            return redirect()->route('partes-diarios.edit', $nuevoParte)
                             ->with('success', 'Parte duplicado correctamente. Modifica los datos necesarios.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al duplicar el parte.');
        }
    }
}
