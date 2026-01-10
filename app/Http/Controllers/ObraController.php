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
use Illuminate\Http\Request;

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

        return view('obras.create', compact('tipos', 'clientes', 'encargados', 'codigoSugerido'));
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
            'fecha_inicio_prevista', 'fecha_fin_prevista'
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

        $obra = Obra::create($validated);

        // Registrar en historial
        ObraHistorial::create([
            'obra_id' => $obra->id,
            'estado_anterior' => null,
            'estado_nuevo' => 'presentada',
            'comentario' => 'Obra creada',
            'cambiado_por' => auth()->id(),
        ]);

        return redirect()->route('obras.show', $obra)
            ->with('success', 'Obra creada exitosamente.');
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
        ]);

        // Estadísticas de la obra
        $stats = [
            'total_trabajadores' => $obra->trabajadoresActivos->count(),
            'total_partes' => $obra->partesDiarios()->count(),
            'total_ingresos' => $obra->ingresos()->sum('importe_total'),
            'total_gastos' => $obra->gastos()->sum('importe_total'),
            'progreso' => $this->calcularProgreso($obra),
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

        return view('obras.edit', compact('obra', 'tipos', 'clientes', 'encargados'));
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
            'fecha_inicio_prevista', 'fecha_fin_prevista', 'fecha_inicio_real', 'fecha_fin_real'
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

        $obra->update($validated);

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
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'descripcion' => 'nullable|string',
            'fecha_documento' => 'nullable|date',
        ], [
            'tipo.required' => 'El tipo de documento es obligatorio.',
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'archivo.required' => 'Debe subir un archivo.',
            'archivo.mimes' => 'El archivo debe ser PDF, imagen o documento Office.',
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
            'descripcion' => $validated['descripcion'],
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
