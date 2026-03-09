<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\ObraTipo;
use App\Models\ObraHito;
use App\Models\ObraDocumento;
use App\Models\ObraHistorial;
use App\Models\Cliente;
use App\Models\Trabajador;
use App\Models\Cuadrilla;
use App\Models\User;
use App\Models\Auditoria;
use App\Exports\ObraEquipoExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ObraController extends Controller
{
    public function index(Request $request)
    {
        $query = Obra::with(['cliente', 'tipo', 'encargado'])
            ->withCount(['trabajadoresActivos', 'partesDiarios']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function ($q2) use ($search) {
                      $q2->where('nombre_comercial', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo de obra
        if ($request->filled('obra_tipo_id')) {
            $query->where('obra_tipo_id', $request->obra_tipo_id);
        }

        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro por encargado
        if ($request->filled('encargado_id')) {
            $query->where('encargado_id', $request->encargado_id);
        }

        $obras = $query->orderByDesc('created_at')->get();

        // Datos para filtros
        $tipos = ObraTipo::orderBy('nombre')->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $encargados = User::role('Encargado')->orderBy('name')->get();

        // Estadísticas
        $stats = [
            'total' => Obra::count(),
            'en_curso' => Obra::where('estado', 'en_curso')->count(),
            'aprobadas' => Obra::where('estado', 'aprobada')->count(),
            'finalizadas' => Obra::where('estado', 'finalizada')->count(),
            'presupuesto_total' => Obra::whereIn('estado', ['en_curso', 'aprobada'])->sum('presupuesto'),
        ];

        return view('obras.index', compact('obras', 'tipos', 'clientes', 'encargados', 'stats'));
    }

    public function create()
    {
        $tipos = ObraTipo::orderBy('nombre')->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $encargados = User::role('Encargado')->orderBy('name')->get();

        // Generar código sugerido
        $año = date('Y');
        $ultimaObra = Obra::where('codigo', 'like', "OBR-{$año}-%")->orderByDesc('codigo')->first();
        if ($ultimaObra) {
            $numero = intval(substr($ultimaObra->codigo, -4)) + 1;
        } else {
            $numero = 1;
        }
        $codigoSugerido = sprintf("OBR-%s-%04d", $año, $numero);

        // Obras con conceptos de producción para copiar
        $obrasConConceptos = Obra::whereHas('conceptosProduccion')
            ->orderBy('nombre')
            ->get();

        return view('obras.create', compact('tipos', 'clientes', 'encargados', 'codigoSugerido', 'obrasConConceptos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:obras',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cliente_id' => 'required|exists:clientes,id',
            'obra_tipo_id' => 'nullable|exists:obra_tipos,id',
            'direccion' => 'nullable|string|max:500',
            'localidad' => 'nullable|string|max:150',
            'provincia' => 'nullable|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'coordenadas_lat' => 'nullable|numeric|between:-90,90',
            'coordenadas_lng' => 'nullable|numeric|between:-180,180',
            'linea' => 'nullable|string|max:100',
            'trayecto' => 'nullable|string|max:255',
            'pk_inicio' => 'nullable|string|max:20',
            'pk_fin' => 'nullable|string|max:20',
            'gerencia_jefatura' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:100',
            'fecha_inicio_prevista' => 'nullable|date',
            'fecha_fin_prevista' => 'nullable|date|after_or_equal:fecha_inicio_prevista',
            'fecha_facturacion_inicio' => 'nullable|date',
            'fecha_facturacion_fin' => 'nullable|date|after_or_equal:fecha_facturacion_inicio',
            'presupuesto' => 'nullable|numeric|min:0',
            'coste_estimado' => 'nullable|numeric|min:0',
            'riesgo_operativo' => 'nullable|in:bajo,medio,alto',
            'tiene_penalizaciones' => 'boolean',
            'importe_penalizacion_prevista' => 'nullable|numeric|min:0',
            'centro_coste' => 'nullable|string|max:50',
            'encargado_id' => 'nullable|exists:users,id',
            'notas' => 'nullable|string',
        ], [
            'codigo.required' => 'El código de obra es obligatorio.',
            'codigo.unique' => 'Este código de obra ya existe.',
            'nombre.required' => 'El nombre de la obra es obligatorio.',
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'coordenadas_lat.between' => 'La latitud debe estar entre -90 y 90.',
            'coordenadas_lng.between' => 'La longitud debe estar entre -180 y 180.',
        ]);

        // Convertir strings vacíos a null para campos numéricos
        $nullableFields = [
            'coordenadas_lat', 'coordenadas_lng', 'presupuesto', 'coste_estimado',
            'importe_penalizacion_prevista', 'obra_tipo_id', 'encargado_id',
            'fecha_inicio_prevista', 'fecha_fin_prevista',
            'fecha_facturacion_inicio', 'fecha_facturacion_fin'
        ];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $validated['estado'] = 'presentada';
        $validated['tiene_penalizaciones'] = $request->boolean('tiene_penalizaciones', false);

        // Calcular margen previsto
        if (!empty($validated['presupuesto']) && !empty($validated['coste_estimado'])) {
            $validated['margen_previsto'] = $validated['presupuesto'] - $validated['coste_estimado'];
        }

        DB::beginTransaction();
        try {
            $obra = Obra::create($validated);

            // Registrar en historial
            ObraHistorial::create([
                'obra_id' => $obra->id,
                'estado_anterior' => null,
                'estado_nuevo' => 'presentada',
                'comentario' => 'Obra creada',
                'cambiado_por' => auth()->id(),
            ]);

            // Registrar en auditoría
            Auditoria::registrar('crear', 'obras', $obra->id, null, $obra->toArray());

            // Crear conceptos de producción si se proporcionaron
            if ($request->has('conceptos') && is_array($request->conceptos)) {
                foreach ($request->conceptos as $concepto) {
                    if (!empty($concepto['codigo']) && !empty($concepto['nombre'])) {
                        $obra->conceptosProduccion()->create([
                            'codigo' => $concepto['codigo'],
                            'nombre' => $concepto['nombre'],
                            'descripcion' => $concepto['descripcion'] ?? null,
                            'categoria' => $concepto['categoria'] ?? 'otro',
                            'unidad' => $concepto['unidad'] ?? 'm2',
                            'precio_unitario' => $concepto['precio_unitario'] ?? 0,
                            'activo' => true,
                            'orden' => $concepto['orden'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('obras.show', $obra)
                ->with('success', 'Obra creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear la obra: ' . $e->getMessage()]);
        }
    }

    public function show(Obra $obra)
    {
        $obra->load([
            'cliente',
            'tipo',
            'encargado',
            'hitos',
            'documentos',
            'historial.cambiadoPor',
            'trabajadoresActivos',
            'cuadrillas' => function ($q) {
                $q->wherePivot('activo', true);
            },
            'partesDiarios' => function ($q) {
                $q->orderByDesc('fecha')->limit(10);
            },
            'conceptosProduccion' => function ($q) {
                $q->orderBy('orden')->orderBy('codigo');
            },
            'discrepancias' => function ($q) {
                $q->orderByDesc('periodo_mes')->limit(12);
            },
        ]);

        // Estadísticas de la obra
        $stats = [
            'total_trabajadores' => $obra->trabajadoresActivos->count(),
            'total_partes' => $obra->partesDiarios()->count(),
            'total_ingresos' => $obra->ingresos()->sum('importe_total'),
            'total_gastos' => $obra->gastos()->sum('importe_total'),
            'progreso' => $this->calcularProgreso($obra),
            'total_producido' => $obra->importe_producido_acumulado,
            'total_pendiente' => $obra->importe_pendiente_acumulado,
            'total_conceptos' => $obra->conceptosProduccion->count(),
            'conceptos_activos' => $obra->conceptosProduccion->where('activo', true)->count(),
        ];

        // Trabajadores y cuadrillas disponibles para asignar
        $trabajadoresDisponibles = Trabajador::where('activo', true)
            ->whereNotIn('id', $obra->trabajadoresActivos->pluck('id'))
            ->orderBy('apellidos')
            ->get();

        $cuadrillasDisponibles = Cuadrilla::where('activa', true)
            ->whereNotIn('id', $obra->cuadrillas->pluck('id'))
            ->orderBy('nombre')
            ->get();

        return view('obras.show', compact('obra', 'stats', 'trabajadoresDisponibles', 'cuadrillasDisponibles'));
    }

    public function edit(Request $request, Obra $obra)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $obra->id,
                'codigo' => $obra->codigo,
                'nombre' => $obra->nombre,
                'descripcion' => $obra->descripcion,
                'cliente_id' => $obra->cliente_id,
                'obra_tipo_id' => $obra->obra_tipo_id,
                'direccion' => $obra->direccion,
                'localidad' => $obra->localidad,
                'provincia' => $obra->provincia,
                'codigo_postal' => $obra->codigo_postal,
                'coordenadas_lat' => $obra->coordenadas_lat,
                'coordenadas_lng' => $obra->coordenadas_lng,
                'linea' => $obra->linea,
                'trayecto' => $obra->trayecto,
                'pk_inicio' => $obra->pk_inicio,
                'pk_fin' => $obra->pk_fin,
                'gerencia_jefatura' => $obra->gerencia_jefatura,
                'distrito' => $obra->distrito,
                'fecha_inicio_prevista' => $obra->fecha_inicio_prevista?->format('Y-m-d'),
                'fecha_fin_prevista' => $obra->fecha_fin_prevista?->format('Y-m-d'),
                'fecha_inicio_real' => $obra->fecha_inicio_real?->format('Y-m-d'),
                'fecha_fin_real' => $obra->fecha_fin_real?->format('Y-m-d'),
                'fecha_facturacion_inicio' => $obra->fecha_facturacion_inicio?->format('Y-m-d'),
                'fecha_facturacion_fin' => $obra->fecha_facturacion_fin?->format('Y-m-d'),
                'presupuesto' => $obra->presupuesto,
                'coste_estimado' => $obra->coste_estimado,
                'estado' => $obra->estado,
                'riesgo_operativo' => $obra->riesgo_operativo,
                'tiene_penalizaciones' => $obra->tiene_penalizaciones,
                'importe_penalizacion_prevista' => $obra->importe_penalizacion_prevista,
                'centro_coste' => $obra->centro_coste,
                'encargado_id' => $obra->encargado_id,
                'notas' => $obra->notas,
            ]);
        }

        $tipos = ObraTipo::orderBy('nombre')->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $encargados = User::role('Encargado')->orderBy('name')->get();

        // Cargar conceptos de producción
        $obra->load('conceptosProduccion');

        // Obras con conceptos de producción para copiar
        $obrasConConceptos = Obra::where('id', '!=', $obra->id)
            ->whereHas('conceptosProduccion')
            ->orderBy('nombre')
            ->get();

        return view('obras.edit', compact('obra', 'tipos', 'clientes', 'encargados', 'obrasConConceptos'));
    }

    public function update(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:obras,codigo,' . $obra->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cliente_id' => 'required|exists:clientes,id',
            'obra_tipo_id' => 'nullable|exists:obra_tipos,id',
            'direccion' => 'nullable|string|max:500',
            'localidad' => 'nullable|string|max:150',
            'provincia' => 'nullable|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'coordenadas_lat' => 'nullable|numeric|between:-90,90',
            'coordenadas_lng' => 'nullable|numeric|between:-180,180',
            'linea' => 'nullable|string|max:100',
            'trayecto' => 'nullable|string|max:255',
            'pk_inicio' => 'nullable|string|max:20',
            'pk_fin' => 'nullable|string|max:20',
            'gerencia_jefatura' => 'nullable|string|max:50',
            'distrito' => 'nullable|string|max:100',
            'fecha_inicio_prevista' => 'nullable|date',
            'fecha_fin_prevista' => 'nullable|date|after_or_equal:fecha_inicio_prevista',
            'fecha_inicio_real' => 'nullable|date',
            'fecha_fin_real' => 'nullable|date|after_or_equal:fecha_inicio_real',
            'fecha_facturacion_inicio' => 'nullable|date',
            'fecha_facturacion_fin' => 'nullable|date|after_or_equal:fecha_facturacion_inicio',
            'presupuesto' => 'nullable|numeric|min:0',
            'coste_estimado' => 'nullable|numeric|min:0',
            'estado' => 'required|in:presentada,aprobada,en_curso,pausada,finalizada,cancelada',
            'riesgo_operativo' => 'nullable|in:bajo,medio,alto',
            'tiene_penalizaciones' => 'boolean',
            'importe_penalizacion_prevista' => 'nullable|numeric|min:0',
            'centro_coste' => 'nullable|string|max:50',
            'encargado_id' => 'nullable|exists:users,id',
            'notas' => 'nullable|string',
        ], [
            'codigo.required' => 'El código de obra es obligatorio.',
            'codigo.unique' => 'Este código de obra ya existe.',
            'nombre.required' => 'El nombre de la obra es obligatorio.',
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'coordenadas_lat.between' => 'La latitud debe estar entre -90 y 90.',
            'coordenadas_lng.between' => 'La longitud debe estar entre -180 y 180.',
        ]);

        // Convertir strings vacíos a null para campos numéricos
        $nullableFields = [
            'coordenadas_lat', 'coordenadas_lng', 'presupuesto', 'coste_estimado',
            'importe_penalizacion_prevista', 'obra_tipo_id', 'encargado_id',
            'fecha_inicio_prevista', 'fecha_fin_prevista', 'fecha_inicio_real', 'fecha_fin_real',
            'fecha_facturacion_inicio', 'fecha_facturacion_fin'
        ];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $validated['tiene_penalizaciones'] = $request->boolean('tiene_penalizaciones', false);

        // Calcular margen previsto
        if (!empty($validated['presupuesto']) && !empty($validated['coste_estimado'])) {
            $validated['margen_previsto'] = $validated['presupuesto'] - $validated['coste_estimado'];
        }

        // Registrar cambio de estado si aplica
        if ($obra->estado !== $validated['estado']) {
            ObraHistorial::create([
                'obra_id' => $obra->id,
                'estado_anterior' => $obra->estado,
                'estado_nuevo' => $validated['estado'],
                'comentario' => $request->comentario_estado,
                'cambiado_por' => auth()->id(),
            ]);
        }

        // Guardar datos anteriores para auditoría
        $datosAnteriores = $obra->toArray();

        $obra->update($validated);

        // Registrar en auditoría
        Auditoria::registrar('editar', 'obras', $obra->id, $datosAnteriores, $obra->fresh()->toArray());

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Obra actualizada exitosamente.');
    }

    public function destroy(Obra $obra)
    {
        // Verificar que no tiene partes diarios
        if ($obra->partesDiarios()->exists()) {
            return redirect()->route('obras.index')
                ->with('error', 'No se puede eliminar una obra con partes diarios registrados.');
        }

        // Registrar en auditoría antes de eliminar
        Auditoria::registrar('eliminar', 'obras', $obra->id, $obra->toArray(), null);

        $obra->delete();

        return redirect()->route('obras.index')
            ->with('success', 'Obra eliminada exitosamente.');
    }

    // =============================================
    // CAMBIO DE ESTADO
    // =============================================

    public function cambiarEstado(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'estado' => 'required|in:presentada,aprobada,en_curso,pausada,finalizada,cancelada',
            'comentario' => 'nullable|string|max:500',
        ]);

        $estadoAnterior = $obra->estado;

        // Registrar en historial
        ObraHistorial::create([
            'obra_id' => $obra->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $validated['estado'],
            'comentario' => $validated['comentario'],
            'cambiado_por' => auth()->id(),
        ]);

        // Actualizar fechas según el estado
        $actualizacion = ['estado' => $validated['estado']];

        if ($validated['estado'] === 'en_curso' && !$obra->fecha_inicio_real) {
            $actualizacion['fecha_inicio_real'] = now();
        }

        if ($validated['estado'] === 'finalizada' && !$obra->fecha_fin_real) {
            $actualizacion['fecha_fin_real'] = now();
        }

        $obra->update($actualizacion);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Estado de la obra actualizado.');
    }

    // =============================================
    // HITOS
    // =============================================

    public function storeHito(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje_obra' => 'nullable|integer|min:0|max:100',
            'fecha_prevista' => 'nullable|date',
            'importe_cobro' => 'nullable|numeric|min:0',
        ], [
            'nombre.required' => 'El nombre del hito es obligatorio.',
        ]);

        $validated['obra_id'] = $obra->id;
        $validated['orden'] = $obra->hitos()->max('orden') + 1;

        ObraHito::create($validated);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Hito agregado exitosamente.');
    }

    public function completarHito(Request $request, Obra $obra, ObraHito $hito)
    {
        $hito->update([
            'completado' => true,
            'fecha_completado' => now(),
        ]);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Hito marcado como completado.');
    }

    public function destroyHito(Obra $obra, ObraHito $hito)
    {
        $hito->delete();

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Hito eliminado.');
    }

    // =============================================
    // DOCUMENTOS
    // =============================================

    public function storeDocumento(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50',
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|max:10240',
            'descripcion' => 'nullable|string',
            'fecha_documento' => 'nullable|date',
        ], [
            'tipo.required' => 'El tipo de documento es obligatorio.',
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'archivo.required' => 'Debe subir un archivo.',
            'archivo.max' => 'El archivo no puede superar los 10MB.',
        ]);

        // Guardar archivo en public/uploads
        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
        $rutaCarpeta = 'uploads/obras/' . $obra->id . '/documentos';
        $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

        ObraDocumento::create([
            'obra_id' => $obra->id,
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
            'descripcion' => $validated['descripcion'] ?? null,
            'fecha_documento' => $validated['fecha_documento'] ?? now(),
            'subido_por' => auth()->id(),
        ]);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Documento subido exitosamente.');
    }

    public function destroyDocumento(Obra $obra, ObraDocumento $documento)
    {
        // Eliminar archivo físico
        if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
            unlink(public_path($documento->archivo_path));
        }

        $documento->delete();

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Documento eliminado.');
    }

    // =============================================
    // ASIGNACIÓN DE TRABAJADORES
    // =============================================

    public function addTrabajador(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'rol' => 'nullable|string|max:100',
        ]);

        // Verificar si ya está asignado
        $existente = $obra->trabajadores()
            ->where('trabajadores.id', $validated['trabajador_id'])
            ->wherePivot('activo', true)
            ->first();

        if ($existente) {
            return redirect()->route('obras.show', $obra)
                ->with('error', 'El trabajador ya está asignado a esta obra.');
        }

        $obra->trabajadores()->attach($validated['trabajador_id'], [
            'fecha_inicio' => now(),
            'rol' => $validated['rol'],
            'activo' => true,
        ]);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Trabajador asignado a la obra.');
    }

    public function removeTrabajador(Obra $obra, Trabajador $trabajador)
    {
        $obra->trabajadores()->updateExistingPivot($trabajador->id, [
            'activo' => false,
            'fecha_fin' => now(),
        ]);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Trabajador desasignado de la obra.');
    }

    // =============================================
    // ASIGNACIÓN DE CUADRILLAS
    // =============================================

    public function addCuadrilla(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'cuadrilla_id' => 'required|exists:cuadrillas,id',
        ]);

        // Verificar si ya está asignada
        $existente = $obra->cuadrillas()
            ->where('cuadrillas.id', $validated['cuadrilla_id'])
            ->wherePivot('activo', true)
            ->first();

        if ($existente) {
            return redirect()->route('obras.show', $obra)
                ->with('error', 'La cuadrilla ya está asignada a esta obra.');
        }

        $obra->cuadrillas()->attach($validated['cuadrilla_id'], [
            'fecha_inicio' => now(),
            'activo' => true,
        ]);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Cuadrilla asignada a la obra.');
    }

    public function removeCuadrilla(Obra $obra, Cuadrilla $cuadrilla)
    {
        $obra->cuadrillas()->updateExistingPivot($cuadrilla->id, [
            'activo' => false,
            'fecha_fin' => now(),
        ]);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Cuadrilla desasignada de la obra.');
    }

    // =============================================
    // EXPORTAR EQUIPO
    // =============================================

    public function exportEquipo(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        $fechaDesde = $validated['fecha_desde'];
        $fechaHasta = $validated['fecha_hasta'];

        // A) Trabajadores asignados directamente
        $directWorkers = DB::table('obra_trabajadores')
            ->where('obra_id', $obra->id)
            ->where('fecha_inicio', '<=', $fechaHasta)
            ->where(function ($q) use ($fechaDesde) {
                $q->whereNull('fecha_fin')
                  ->orWhere('fecha_fin', '>=', $fechaDesde);
            })
            ->get(['trabajador_id', 'fecha_inicio', 'fecha_fin', 'rol']);

        // B) Trabajadores via cuadrillas
        $cuadrillaWorkers = DB::table('obra_cuadrillas')
            ->join('cuadrilla_trabajadores', 'obra_cuadrillas.cuadrilla_id', '=', 'cuadrilla_trabajadores.cuadrilla_id')
            ->join('cuadrillas', 'cuadrillas.id', '=', 'obra_cuadrillas.cuadrilla_id')
            ->where('obra_cuadrillas.obra_id', $obra->id)
            ->where('obra_cuadrillas.fecha_inicio', '<=', $fechaHasta)
            ->where(function ($q) use ($fechaDesde) {
                $q->whereNull('obra_cuadrillas.fecha_fin')
                  ->orWhere('obra_cuadrillas.fecha_fin', '>=', $fechaDesde);
            })
            ->where('cuadrilla_trabajadores.fecha_incorporacion', '<=', $fechaHasta)
            ->where(function ($q) use ($fechaDesde) {
                $q->whereNull('cuadrilla_trabajadores.fecha_salida')
                  ->orWhere('cuadrilla_trabajadores.fecha_salida', '>=', $fechaDesde);
            })
            ->get([
                'cuadrilla_trabajadores.trabajador_id',
                'obra_cuadrillas.fecha_inicio as oc_fecha_inicio',
                'obra_cuadrillas.fecha_fin as oc_fecha_fin',
                'cuadrilla_trabajadores.fecha_incorporacion',
                'cuadrilla_trabajadores.fecha_salida',
                'cuadrillas.nombre as cuadrilla_nombre',
            ]);

        // Construir mapa de trabajadores con metadata
        $workerMap = [];

        foreach ($directWorkers as $dw) {
            $id = $dw->trabajador_id;
            $workerMap[$id] = [
                'asignacion' => 'Directa',
                'rol' => $dw->rol ?? 'Operario',
                'fecha_inicio' => $dw->fecha_inicio,
                'fecha_fin' => $dw->fecha_fin,
            ];
        }

        foreach ($cuadrillaWorkers as $cw) {
            $id = $cw->trabajador_id;
            // Fecha efectiva = interseccion de cuadrilla-obra y trabajador-cuadrilla
            $effectiveStart = max($cw->oc_fecha_inicio, $cw->fecha_incorporacion);
            $effectiveEnd = $cw->oc_fecha_fin && $cw->fecha_salida
                ? min($cw->oc_fecha_fin, $cw->fecha_salida)
                : ($cw->oc_fecha_fin ?? $cw->fecha_salida);

            if (isset($workerMap[$id])) {
                // Ya existe por asignacion directa, agregar cuadrilla al label
                $current = $workerMap[$id]['asignacion'];
                if (strpos($current, $cw->cuadrilla_nombre) === false) {
                    $workerMap[$id]['asignacion'] = $current . ' + ' . $cw->cuadrilla_nombre;
                }
            } else {
                $workerMap[$id] = [
                    'asignacion' => $cw->cuadrilla_nombre,
                    'rol' => 'Operario',
                    'fecha_inicio' => $effectiveStart,
                    'fecha_fin' => $effectiveEnd,
                ];
            }
        }

        if (empty($workerMap)) {
            // Retornar excel vacio con solo headers
            $rows = collect();
            $filename = 'equipo_' . Str::slug($obra->codigo) . '_' . $fechaDesde . '_' . $fechaHasta . '.xlsx';
            return Excel::download(new ObraEquipoExport($rows, $obra->nombre), $filename);
        }

        $allWorkerIds = array_keys($workerMap);

        // C) Horas de fichajes
        $horasData = DB::table('fichajes')
            ->where('obra_id', $obra->id)
            ->whereIn('trabajador_id', $allWorkerIds)
            ->whereBetween('fecha', [$fechaDesde, $fechaHasta])
            ->groupBy('trabajador_id')
            ->selectRaw('trabajador_id, COALESCE(SUM(horas_trabajadas), 0) as total_horas, COALESCE(SUM(horas_extra), 0) as total_extra, COUNT(*) as total_fichajes')
            ->get()
            ->keyBy('trabajador_id');

        // D) Cargar datos de trabajadores
        $trabajadores = Trabajador::withTrashed()
            ->whereIn('id', $allWorkerIds)
            ->get()
            ->keyBy('id');

        // E) Construir filas del excel
        $rows = collect();
        foreach ($workerMap as $trabajadorId => $meta) {
            $trabajador = $trabajadores->get($trabajadorId);
            if (!$trabajador) continue;

            $horas = $horasData->get($trabajadorId);

            // Calcular dias en obra dentro del periodo filtrado
            $assignStart = $meta['fecha_inicio'] ? Carbon::parse($meta['fecha_inicio']) : Carbon::parse($fechaDesde);
            $assignEnd = $meta['fecha_fin'] ? Carbon::parse($meta['fecha_fin']) : Carbon::parse($fechaHasta);
            $effectiveStart = Carbon::parse(max($assignStart->format('Y-m-d'), $fechaDesde));
            $effectiveEnd = Carbon::parse(min($assignEnd->format('Y-m-d'), $fechaHasta));
            $dias = max(1, $effectiveStart->diffInDays($effectiveEnd) + 1);

            $rows->push([
                'dni' => $trabajador->dni,
                'nombre_completo' => $trabajador->nombre . ' ' . $trabajador->apellidos,
                'categoria' => $trabajador->categoria_convenio,
                'asignacion' => $meta['asignacion'],
                'rol' => $meta['rol'],
                'fecha_inicio' => $meta['fecha_inicio'] ? Carbon::parse($meta['fecha_inicio'])->format('d/m/Y') : '-',
                'fecha_fin' => $meta['fecha_fin'] ? Carbon::parse($meta['fecha_fin'])->format('d/m/Y') : null,
                'dias_en_obra' => $dias,
                'horas_trabajadas' => $horas->total_horas ?? 0,
                'horas_extra' => $horas->total_extra ?? 0,
                'total_fichajes' => $horas->total_fichajes ?? 0,
            ]);
        }

        // Ordenar por nombre
        $rows = $rows->sortBy('nombre_completo')->values();

        $periodo = Carbon::parse($fechaDesde)->format('d-m-Y') . '_' . Carbon::parse($fechaHasta)->format('d-m-Y');
        $filename = 'equipo_' . Str::slug($obra->codigo) . '_' . $periodo . '.xlsx';

        return Excel::download(new ObraEquipoExport($rows, $obra->nombre, $periodo), $filename);
    }

    // =============================================
    // HELPERS
    // =============================================

    private function calcularProgreso(Obra $obra): int
    {
        $hitos = $obra->hitos;

        if ($hitos->isEmpty()) {
            // Si no hay hitos, calcular por fechas
            if (!$obra->fecha_inicio_real || !$obra->fecha_fin_prevista) {
                return 0;
            }

            $inicio = $obra->fecha_inicio_real;
            $fin = $obra->fecha_fin_prevista;
            $hoy = now();

            if ($hoy >= $fin) {
                return 100;
            }

            $totalDias = $inicio->diffInDays($fin);
            $diasTranscurridos = $inicio->diffInDays($hoy);

            return $totalDias > 0 ? min(100, round(($diasTranscurridos / $totalDias) * 100)) : 0;
        }

        // Calcular por hitos completados
        $completados = $hitos->where('completado', true)->count();
        return $hitos->count() > 0 ? round(($completados / $hitos->count()) * 100) : 0;
    }
}
