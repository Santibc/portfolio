<?php

namespace App\Http\Controllers;

use App\Models\ParteDiario;
use App\Models\ParteDiarioTrabajador;
use App\Models\ParteDiarioProduccion;
use App\Models\ParteDiarioDocumento;
use App\Models\ObraConceptoProduccion;
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
        // Determinar rango de fechas
        $fechaDesde = $request->filled('fecha_desde')
            ? $request->fecha_desde
            : now()->startOfMonth()->format('Y-m-d');
        $fechaHasta = $request->filled('fecha_hasta')
            ? $request->fecha_hasta
            : now()->endOfMonth()->format('Y-m-d');

        $query = ParteDiario::with(['obra', 'creadoPor', 'producciones.concepto'])
                            ->withCount('documentos');

        // Filtro de fechas unificado (diarios + mensuales)
        $query->enPeriodo($fechaDesde, $fechaHasta);

        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('jornada')) {
            $query->where('jornada', $request->jornada);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $partes = $query->orderBy('fecha', 'desc')
                        ->paginate(25);

        // Calcular producción por categoría desde parte_diario_producciones
        $statsQuery = ParteDiarioProduccion::query()
            ->join('partes_diarios', 'parte_diario_producciones.parte_diario_id', '=', 'partes_diarios.id')
            ->join('obra_conceptos_produccion', 'parte_diario_producciones.concepto_produccion_id', '=', 'obra_conceptos_produccion.id');

        $this->applyPeriodoFilter($statsQuery, $fechaDesde, $fechaHasta);

        if ($request->filled('obra_id')) {
            $statsQuery->where('partes_diarios.obra_id', $request->obra_id);
        }

        $produccionPorCategoria = (clone $statsQuery)
            ->select('obra_conceptos_produccion.categoria', DB::raw('SUM(parte_diario_producciones.cantidad) as total'))
            ->groupBy('obra_conceptos_produccion.categoria')
            ->pluck('total', 'categoria')
            ->toArray();

        // Obtener categorías activas con sus unidades predominantes
        $categoriasActivas = ObraConceptoProduccion::query()
            ->whereHas('producciones', function($q) use ($fechaDesde, $fechaHasta, $request) {
                $q->whereHas('parteDiario', function($q2) use ($fechaDesde, $fechaHasta, $request) {
                    $q2->enPeriodo($fechaDesde, $fechaHasta);
                    if ($request->filled('obra_id')) {
                        $q2->where('obra_id', $request->obra_id);
                    }
                });
            })
            ->select('categoria', 'unidad')
            ->distinct()
            ->get()
            ->groupBy('categoria')
            ->map(fn($items) => $items->first()->unidad)
            ->toArray();

        // Si no hay categorías activas, mostrar las categorías por defecto
        if (empty($categoriasActivas)) {
            $categoriasActivas = [
                'desbroce' => 'm2',
                'herbicida' => 'm2',
                'tala' => 'unidades',
                'poda' => 'unidades',
            ];
        }

        // Estadísticas del período
        $stats = [
            'total_partes' => $partes->total(),
            'borradores' => ParteDiario::borradores()
                ->enPeriodo($fechaDesde, $fechaHasta)
                ->when($request->filled('obra_id'), fn($q) => $q->where('obra_id', $request->obra_id))
                ->count(),
            'pendientes_validar' => ParteDiario::completados()
                ->enPeriodo($fechaDesde, $fechaHasta)
                ->when($request->filled('obra_id'), fn($q) => $q->where('obra_id', $request->obra_id))
                ->count(),
            'produccion_por_categoria' => $produccionPorCategoria,
            'categorias_activas' => $categoriasActivas,
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
                     ->with(['cliente', 'conceptosProduccion' => function($q) {
                         $q->where('activo', true)->orderBy('orden');
                     }])
                     ->orderBy('nombre')
                     ->get();

        // Pre-seleccionar obra si viene en el request
        $obraSeleccionada = null;
        if ($request->filled('obra_id')) {
            $obraSeleccionada = Obra::with(['conceptosProduccion' => function($q) {
                $q->where('activo', true)->orderBy('orden');
            }])->find($request->obra_id);
        }

        $tipo = $request->get('tipo', 'diario');

        return view('partes-diarios.create', compact('obras', 'obraSeleccionada', 'tipo'));
    }

    /**
     * Store a newly created parte diario.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:diario,mensual',
            'obra_id' => 'required|exists:obras,id',
            'fecha' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha|required_if:tipo,mensual',
            'jornada' => $request->input('tipo') === 'mensual' ? 'nullable|in:diurna,nocturna' : 'required|in:diurna,nocturna',
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
            'producciones' => 'nullable|array',
            'producciones.*.concepto_id' => 'required|exists:obra_conceptos_produccion,id',
            'producciones.*.cantidad' => 'required|numeric|min:0',
            'documentos' => 'nullable|array',
            'documentos.*' => 'file|max:10240',
        ]);

        // Aviso de coexistencia si hay partes en el mismo periodo
        $warningMsg = null;
        if ($validated['tipo'] === 'mensual') {
            $existentes = ParteDiario::where('obra_id', $validated['obra_id'])
                ->where('tipo', 'diario')
                ->whereBetween('fecha', [$validated['fecha'], $validated['fecha_fin']])
                ->count();
            if ($existentes > 0) {
                $warningMsg = "Existen {$existentes} parte(s) diario(s) en este periodo para esta obra.";
            }
        } else {
            $existentes = ParteDiario::where('obra_id', $validated['obra_id'])
                ->where('tipo', 'mensual')
                ->where('fecha', '<=', $validated['fecha'])
                ->where('fecha_fin', '>=', $validated['fecha'])
                ->count();
            if ($existentes > 0) {
                $warningMsg = "Existe un parte mensual que cubre esta fecha para esta obra.";
            }
        }

        DB::beginTransaction();
        try {
            $parte = ParteDiario::create([
                'obra_id' => $validated['obra_id'],
                'fecha' => $validated['fecha'],
                'tipo' => $validated['tipo'],
                'fecha_fin' => $validated['tipo'] === 'mensual' ? $validated['fecha_fin'] : null,
                'jornada' => $validated['tipo'] === 'mensual' ? ($validated['jornada'] ?? null) : $validated['jornada'],
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

            // Agregar producciones dinámicas
            if (!empty($validated['producciones'])) {
                foreach ($validated['producciones'] as $produccion) {
                    if ($produccion['cantidad'] > 0) {
                        $concepto = ObraConceptoProduccion::find($produccion['concepto_id']);

                        ParteDiarioProduccion::create([
                            'parte_diario_id' => $parte->id,
                            'concepto_produccion_id' => $concepto->id,
                            'cantidad' => $produccion['cantidad'],
                            'precio_unitario' => $concepto->precio_unitario, // snapshot
                            'importe_calculado' => $produccion['cantidad'] * $concepto->precio_unitario,
                        ]);
                    }
                }
            }

            // Guardar documentos adjuntos
            if ($request->hasFile('documentos')) {
                $rutaCarpeta = 'uploads/partes-diarios/' . $parte->id;
                foreach ($request->file('documentos') as $archivo) {
                    $nombreOriginal = $archivo->getClientOriginalName();
                    $nombreArchivo = time() . '_' . $nombreOriginal;
                    $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

                    ParteDiarioDocumento::create([
                        'parte_diario_id' => $parte->id,
                        'nombre' => pathinfo($nombreOriginal, PATHINFO_FILENAME),
                        'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
                        'archivo_nombre_original' => $nombreOriginal,
                        'subido_por' => Auth::id(),
                    ]);
                }
            }

            // Actualizar importe total del parte
            $parte->calcularYActualizarImporte();

            DB::commit();

            $tipoLabel = $parte->es_mensual ? 'mensual' : 'diario';
            $redirect = redirect()->route('partes-diarios.show', $parte)
                                  ->with('success', "Parte {$tipoLabel} creado correctamente.");

            if ($warningMsg) {
                $redirect->with('warning', $warningMsg);
            }

            return $redirect;
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
            'obra.cuadrillas' => function ($q) {
                $q->wherePivot('activo', true);
            },
            'obra.cuadrillas.trabajadoresActivos',
            'obra.trabajadoresActivos',
            'creadoPor',
            'trabajadores.trabajador',
            'lineas',
            'herbicidas',
            'producciones.concepto',
            'documentos',
        ]);

        // Agrupar trabajadores del parte por categoría
        $trabajadorIdsDelParte = $partes_diario->trabajadores->pluck('trabajador_id')->toArray();

        // 1. Trabajadores agrupados por cuadrilla
        $cuadrillasConTrabajadores = [];
        $workerIdsInCuadrillas = [];
        foreach ($partes_diario->obra->cuadrillas as $cuadrilla) {
            $membersInParte = $cuadrilla->trabajadoresActivos
                ->filter(fn ($t) => in_array($t->id, $trabajadorIdsDelParte));
            if ($membersInParte->isNotEmpty()) {
                $cuadrillasConTrabajadores[] = [
                    'cuadrilla' => $cuadrilla,
                    'trabajadores' => $membersInParte,
                ];
                $workerIdsInCuadrillas = array_merge($workerIdsInCuadrillas, $membersInParte->pluck('id')->toArray());
            }
        }
        $workerIdsInCuadrillas = array_unique($workerIdsInCuadrillas);

        // 2. Trabajadores directos de la obra (no via cuadrilla)
        $obraDirectIds = $partes_diario->obra->trabajadoresActivos->pluck('id')->toArray();
        $directWorkers = $partes_diario->trabajadores
            ->filter(fn ($pt) => in_array($pt->trabajador_id, $obraDirectIds) && !in_array($pt->trabajador_id, $workerIdsInCuadrillas));

        // 3. Trabajadores externos (no asignados a la obra)
        $allObraWorkerIds = array_unique(array_merge($workerIdsInCuadrillas, $obraDirectIds));
        $externalWorkers = $partes_diario->trabajadores
            ->filter(fn ($pt) => !in_array($pt->trabajador_id, $allObraWorkerIds));

        return view('partes-diarios.show', compact(
            'partes_diario',
            'cuadrillasConTrabajadores',
            'directWorkers',
            'externalWorkers'
        ));
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
                     ->with(['conceptosProduccion' => function($q) {
                         $q->where('activo', true)->orderBy('orden');
                     }])
                     ->orderBy('nombre')
                     ->get();

        $partes_diario->load(['trabajadores', 'producciones.concepto', 'obra.conceptosProduccion', 'documentos']);

        return view('partes-diarios.edit', compact('partes_diario', 'obras'));
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
            'jornada' => $partes_diario->es_mensual ? 'nullable|in:diurna,nocturna' : 'required|in:diurna,nocturna',
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
            'producciones' => 'nullable|array',
            'producciones.*.concepto_id' => 'required|exists:obra_conceptos_produccion,id',
            'producciones.*.cantidad' => 'required|numeric|min:0',
            'documentos' => 'nullable|array',
            'documentos.*' => 'file|max:10240',
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

            // Actualizar producciones dinámicas
            $partes_diario->producciones()->delete();
            if (!empty($validated['producciones'])) {
                foreach ($validated['producciones'] as $produccion) {
                    if ($produccion['cantidad'] > 0) {
                        $concepto = ObraConceptoProduccion::find($produccion['concepto_id']);

                        ParteDiarioProduccion::create([
                            'parte_diario_id' => $partes_diario->id,
                            'concepto_produccion_id' => $concepto->id,
                            'cantidad' => $produccion['cantidad'],
                            'precio_unitario' => $concepto->precio_unitario, // snapshot
                            'importe_calculado' => $produccion['cantidad'] * $concepto->precio_unitario,
                        ]);
                    }
                }
            }

            // Guardar nuevos documentos adjuntos
            if ($request->hasFile('documentos')) {
                $rutaCarpeta = 'uploads/partes-diarios/' . $partes_diario->id;
                foreach ($request->file('documentos') as $archivo) {
                    $nombreOriginal = $archivo->getClientOriginalName();
                    $nombreArchivo = time() . '_' . $nombreOriginal;
                    $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

                    ParteDiarioDocumento::create([
                        'parte_diario_id' => $partes_diario->id,
                        'nombre' => pathinfo($nombreOriginal, PATHINFO_FILENAME),
                        'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
                        'archivo_nombre_original' => $nombreOriginal,
                        'subido_por' => Auth::id(),
                    ]);
                }
            }

            // Actualizar importe total del parte
            $partes_diario->calcularYActualizarImporte();

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
        $rules = ['fecha' => 'required|date'];
        if ($partes_diario->es_mensual) {
            $rules['fecha_fin'] = 'required|date|after_or_equal:fecha';
        }
        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $nuevoParte = $partes_diario->replicate();
            $nuevoParte->fecha = $validated['fecha'];
            $nuevoParte->fecha_fin = $partes_diario->es_mensual ? $validated['fecha_fin'] : null;
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

            // Duplicar producciones
            foreach ($partes_diario->producciones as $produccion) {
                ParteDiarioProduccion::create([
                    'parte_diario_id' => $nuevoParte->id,
                    'concepto_produccion_id' => $produccion->concepto_produccion_id,
                    'cantidad' => $produccion->cantidad,
                    'precio_unitario' => $produccion->precio_unitario,
                    'importe_calculado' => $produccion->importe_calculado,
                    'observaciones' => $produccion->observaciones,
                ]);
            }

            // Recalcular importe total
            $nuevoParte->calcularYActualizarImporte();

            DB::commit();

            return redirect()->route('partes-diarios.edit', $nuevoParte)
                             ->with('success', 'Parte duplicado correctamente. Modifica los datos necesarios.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al duplicar el parte: ' . $e->getMessage());
        }
    }

    /**
     * Subir documento a un parte diario.
     */
    public function storeDocumento(Request $request, ParteDiario $partes_diario)
    {
        $request->validate([
            'documentos' => 'required|array',
            'documentos.*' => 'file|max:10240',
        ]);

        $rutaCarpeta = 'uploads/partes-diarios/' . $partes_diario->id;
        foreach ($request->file('documentos') as $archivo) {
            $nombreOriginal = $archivo->getClientOriginalName();
            $nombreArchivo = time() . '_' . $nombreOriginal;
            $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

            ParteDiarioDocumento::create([
                'parte_diario_id' => $partes_diario->id,
                'nombre' => pathinfo($nombreOriginal, PATHINFO_FILENAME),
                'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
                'archivo_nombre_original' => $nombreOriginal,
                'subido_por' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Documento(s) subido(s) correctamente.');
    }

    /**
     * Eliminar documento de un parte diario.
     */
    public function destroyDocumento(ParteDiario $partes_diario, ParteDiarioDocumento $documento)
    {
        if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
            unlink(public_path($documento->archivo_path));
        }

        $documento->delete();

        return back()->with('success', 'Documento eliminado correctamente.');
    }

    /**
     * Aplicar filtro de periodo para queries con JOIN (diarios + mensuales).
     */
    private function applyPeriodoFilter($query, $fechaDesde, $fechaHasta, string $alias = 'partes_diarios')
    {
        return $query->where(function ($q) use ($fechaDesde, $fechaHasta, $alias) {
            $q->where(function ($q2) use ($fechaDesde, $fechaHasta, $alias) {
                $q2->where("{$alias}.tipo", 'diario')
                   ->whereBetween("{$alias}.fecha", [$fechaDesde, $fechaHasta]);
            })->orWhere(function ($q2) use ($fechaDesde, $fechaHasta, $alias) {
                $q2->where("{$alias}.tipo", 'mensual')
                   ->where("{$alias}.fecha", '<=', $fechaHasta)
                   ->where("{$alias}.fecha_fin", '>=', $fechaDesde);
            });
        });
    }

    /**
     * AJAX: Get workers categorized by their relationship to an obra.
     */
    public function getTrabajadoresObra(Obra $obra)
    {
        // 1. Cuadrillas activas de la obra con sus trabajadores activos
        $cuadrillas = $obra->cuadrillas()
            ->wherePivot('activo', true)
            ->with(['trabajadoresActivos' => function ($q) {
                $q->orderBy('nombre');
            }, 'capataz'])
            ->get();

        // 2. IDs de trabajadores que pertenecen a alguna cuadrilla de la obra
        $cuadrillaTrabajadorIds = $cuadrillas->flatMap(function ($c) {
            return $c->trabajadoresActivos->pluck('id');
        })->unique()->values()->toArray();

        // 3. Trabajadores directamente asignados a la obra (activos)
        $obraDirectos = $obra->trabajadoresActivos()
            ->where('trabajadores.activo', true)
            ->orderBy('nombre')
            ->get();

        // 4. Lista unificada de todos los trabajadores de la obra (directos + cuadrilla)
        $allObraWorkerIds = collect($cuadrillaTrabajadorIds)
            ->merge($obraDirectos->pluck('id'))
            ->unique()
            ->values()
            ->toArray();

        // Obtener todos los trabajadores de la obra para la pestaña unificada
        $allObraWorkers = Trabajador::where('activo', true)
            ->whereIn('id', $allObraWorkerIds)
            ->orderBy('nombre')
            ->get();

        // 5. Todos los demás trabajadores activos (no asignados a la obra)
        $otrosTrabajadores = Trabajador::where('activo', true)
            ->whereNotIn('id', $allObraWorkerIds)
            ->orderBy('nombre')
            ->get();

        $formatWorker = fn ($t) => [
            'id' => $t->id,
            'nombre' => $t->nombre,
            'apellidos' => $t->apellidos,
            'nombre_completo' => $t->nombre_completo,
        ];

        return response()->json([
            'cuadrillas' => $cuadrillas->map(function ($cuadrilla) use ($formatWorker) {
                return [
                    'id' => $cuadrilla->id,
                    'nombre' => $cuadrilla->nombre,
                    'capataz' => $cuadrilla->capataz ? $cuadrilla->capataz->nombre_completo : null,
                    'trabajadores' => $cuadrilla->trabajadoresActivos->map($formatWorker)->values(),
                ];
            }),
            'obra_trabajadores' => $allObraWorkers->map(function ($t) use ($cuadrillaTrabajadorIds, $obraDirectos, $formatWorker) {
                $data = $formatWorker($t);
                $data['via_cuadrilla'] = in_array($t->id, $cuadrillaTrabajadorIds);
                $directo = $obraDirectos->firstWhere('id', $t->id);
                $data['rol'] = $directo ? $directo->pivot->rol : null;
                return $data;
            }),
            'otros_trabajadores' => $otrosTrabajadores->map($formatWorker)->values(),
        ]);
    }
}
