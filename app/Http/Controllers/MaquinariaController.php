<?php

namespace App\Http\Controllers;

use App\Models\Maquinaria;
use App\Models\MaquinariaTipo;
use App\Models\MaquinariaAsignacion;
use App\Models\MaquinariaInspeccion;
use App\Models\MaquinariaInspeccionItem;
use App\Models\MaquinariaMantenimiento;
use App\Models\MaquinariaDocumento;
use App\Models\MaquinariaChecklistPlantilla;
use App\Models\Obra;
use App\Models\Trabajador;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaquinariaController extends Controller
{
    public function index(Request $request)
    {
        $query = Maquinaria::with(['tipo', 'obraAsignada', 'trabajadorAsignado'])
            ->withCount(['inspecciones', 'mantenimientos']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_interno', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('maquinaria_tipo_id')) {
            $query->where('maquinaria_tipo_id', $request->maquinaria_tipo_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por obra asignada
        if ($request->filled('obra_id')) {
            $query->where('obra_asignada_id', $request->obra_id);
        }

        // Filtro por disponibilidad
        if ($request->filled('disponible') && $request->disponible === '1') {
            $query->disponibles();
        }

        $maquinarias = $query->orderBy('codigo_interno')->get();

        // Datos para filtros
        $tipos = MaquinariaTipo::orderBy('nombre')->get();
        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])->orderBy('nombre')->get();

        // Estadísticas
        $stats = [
            'total' => Maquinaria::count(),
            'operativas' => Maquinaria::where('estado', 'operativa')->count(),
            'en_reparacion' => Maquinaria::where('estado', 'en_reparacion')->count(),
            'baja' => Maquinaria::where('estado', 'baja')->count(),
            'valor_total' => Maquinaria::sum('coste_adquisicion'),
        ];

        return view('maquinaria.index', compact('maquinarias', 'tipos', 'obras', 'stats'));
    }

    public function create()
    {
        $tipos = MaquinariaTipo::orderBy('nombre')->get();
        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])->orderBy('nombre')->get();
        $trabajadores = Trabajador::where('activo', true)->orderBy('apellidos')->get();

        // Generar código sugerido
        $ultimaMaquinaria = Maquinaria::orderByDesc('id')->first();
        $numero = $ultimaMaquinaria ? $ultimaMaquinaria->id + 1 : 1;
        $codigoSugerido = sprintf("MAQ-%04d", $numero);

        return view('maquinaria.create', compact('tipos', 'obras', 'trabajadores', 'codigoSugerido'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'maquinaria_tipo_id' => 'required|exists:maquinaria_tipos,id',
            'codigo_interno' => 'nullable|string|max:50|unique:maquinaria',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => 'nullable|string|max:100',
            'numero_bastidor' => 'nullable|string|max:100',
            'fecha_compra' => 'nullable|date',
            'coste_adquisicion' => 'nullable|numeric|min:0',
            'vida_util_meses' => 'nullable|integer|min:1',
            'coste_hora' => 'nullable|numeric|min:0',
            'tiene_marcado_ce' => 'boolean',
            'tiene_manual' => 'boolean',
            'notas' => 'nullable|string',
        ], [
            'maquinaria_tipo_id.required' => 'Debe seleccionar un tipo de maquinaria.',
            'maquinaria_tipo_id.exists' => 'El tipo de maquinaria seleccionado no existe.',
            'codigo_interno.unique' => 'Este código interno ya está en uso.',
        ]);

        // Convertir strings vacíos a null
        $nullableFields = ['codigo_interno', 'marca', 'modelo', 'numero_serie', 'numero_bastidor',
                          'fecha_compra', 'coste_adquisicion', 'vida_util_meses', 'coste_hora'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        // Calcular amortización diaria
        if (!empty($validated['coste_adquisicion']) && !empty($validated['vida_util_meses'])) {
            $validated['amortizacion_dia'] = round($validated['coste_adquisicion'] / ($validated['vida_util_meses'] * 30), 2);
        }

        $validated['estado'] = 'operativa';
        $validated['tiene_marcado_ce'] = $request->boolean('tiene_marcado_ce', true);
        $validated['tiene_manual'] = $request->boolean('tiene_manual', true);

        $maquinaria = Maquinaria::create($validated);

        // Registrar en auditoría
        Auditoria::registrar('crear', 'maquinaria', $maquinaria->id, null, $maquinaria->toArray());

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Maquinaria registrada exitosamente.');
    }

    public function show(Maquinaria $maquinaria)
    {
        $maquinaria->load([
            'tipo',
            'obraAsignada',
            'trabajadorAsignado',
            'asignaciones.obra',
            'inspecciones.plantilla',
            'inspecciones.realizadoPor',
            'mantenimientos',
            'documentos.subidoPor',
        ]);

        // Plantillas de checklist disponibles para este tipo
        $checklistPlantillas = MaquinariaChecklistPlantilla::with('items')
            ->where(function ($q) use ($maquinaria) {
                $q->where('maquinaria_tipo_id', $maquinaria->maquinaria_tipo_id)
                  ->orWhereNull('maquinaria_tipo_id');
            })
            ->where('activa', true)
            ->get();

        // Obras disponibles para asignar
        $obrasDisponibles = Obra::whereIn('estado', ['en_curso', 'aprobada'])
            ->orderBy('nombre')
            ->get();

        // Trabajadores activos
        $trabajadoresDisponibles = Trabajador::where('activo', true)
            ->orderBy('apellidos')
            ->get();

        // Estadisticas
        $ultimaInspeccion = $maquinaria->inspecciones->sortByDesc('fecha_inspeccion')->first();
        $stats = [
            'total_inspecciones' => $maquinaria->inspecciones->count(),
            'ultima_inspeccion' => $ultimaInspeccion?->fecha_inspeccion?->format('d/m/Y'),
            'total_mantenimientos' => $maquinaria->mantenimientos->count(),
            'coste_mantenimientos' => $maquinaria->mantenimientos->sum('coste'),
            'amortizacion_acumulada' => $this->calcularAmortizacionAcumulada($maquinaria),
        ];

        return view('maquinaria.show', compact(
            'maquinaria',
            'checklistPlantillas',
            'obrasDisponibles',
            'trabajadoresDisponibles',
            'stats'
        ));
    }

    public function edit(Request $request, Maquinaria $maquinaria)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $maquinaria->id,
                'maquinaria_tipo_id' => $maquinaria->maquinaria_tipo_id,
                'codigo_interno' => $maquinaria->codigo_interno,
                'marca' => $maquinaria->marca,
                'modelo' => $maquinaria->modelo,
                'numero_serie' => $maquinaria->numero_serie,
                'numero_bastidor' => $maquinaria->numero_bastidor,
                'fecha_compra' => $maquinaria->fecha_compra?->format('Y-m-d'),
                'coste_adquisicion' => $maquinaria->coste_adquisicion,
                'vida_util_meses' => $maquinaria->vida_util_meses,
                'amortizacion_dia' => $maquinaria->amortizacion_dia,
                'coste_hora' => $maquinaria->coste_hora,
                'estado' => $maquinaria->estado,
                'tiene_marcado_ce' => $maquinaria->tiene_marcado_ce,
                'tiene_manual' => $maquinaria->tiene_manual,
                'notas' => $maquinaria->notas,
            ]);
        }

        $tipos = MaquinariaTipo::orderBy('nombre')->get();
        $obras = Obra::whereIn('estado', ['en_curso', 'aprobada'])->orderBy('nombre')->get();
        $trabajadores = Trabajador::where('activo', true)->orderBy('apellidos')->get();

        return view('maquinaria.edit', compact('maquinaria', 'tipos', 'obras', 'trabajadores'));
    }

    public function update(Request $request, Maquinaria $maquinaria)
    {
        $validated = $request->validate([
            'maquinaria_tipo_id' => 'required|exists:maquinaria_tipos,id',
            'codigo_interno' => 'nullable|string|max:50|unique:maquinaria,codigo_interno,' . $maquinaria->id,
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_serie' => 'nullable|string|max:100',
            'numero_bastidor' => 'nullable|string|max:100',
            'fecha_compra' => 'nullable|date',
            'coste_adquisicion' => 'nullable|numeric|min:0',
            'vida_util_meses' => 'nullable|integer|min:1',
            'coste_hora' => 'nullable|numeric|min:0',
            'estado' => 'required|in:operativa,en_reparacion,baja',
            'tiene_marcado_ce' => 'boolean',
            'tiene_manual' => 'boolean',
            'notas' => 'nullable|string',
        ], [
            'maquinaria_tipo_id.required' => 'Debe seleccionar un tipo de maquinaria.',
            'codigo_interno.unique' => 'Este código interno ya está en uso.',
        ]);

        // Convertir strings vacíos a null
        $nullableFields = ['codigo_interno', 'marca', 'modelo', 'numero_serie', 'numero_bastidor',
                          'fecha_compra', 'coste_adquisicion', 'vida_util_meses', 'coste_hora'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        // Recalcular amortización diaria
        if (!empty($validated['coste_adquisicion']) && !empty($validated['vida_util_meses'])) {
            $validated['amortizacion_dia'] = round($validated['coste_adquisicion'] / ($validated['vida_util_meses'] * 30), 2);
        } else {
            $validated['amortizacion_dia'] = null;
        }

        $validated['tiene_marcado_ce'] = $request->boolean('tiene_marcado_ce', false);
        $validated['tiene_manual'] = $request->boolean('tiene_manual', false);

        // Guardar datos anteriores para auditoría
        $datosAnteriores = $maquinaria->toArray();

        $maquinaria->update($validated);

        // Registrar en auditoría
        Auditoria::registrar('editar', 'maquinaria', $maquinaria->id, $datosAnteriores, $maquinaria->fresh()->toArray());

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Maquinaria actualizada exitosamente.');
    }

    public function destroy(Maquinaria $maquinaria)
    {
        // Verificar que no tiene inspecciones o mantenimientos
        if ($maquinaria->inspecciones()->exists()) {
            return redirect()->route('maquinaria.index')
                ->with('error', 'No se puede eliminar maquinaria con inspecciones registradas.');
        }

        if ($maquinaria->mantenimientos()->exists()) {
            return redirect()->route('maquinaria.index')
                ->with('error', 'No se puede eliminar maquinaria con mantenimientos registrados.');
        }

        // Registrar en auditoría antes de eliminar
        Auditoria::registrar('eliminar', 'maquinaria', $maquinaria->id, $maquinaria->toArray(), null);

        $maquinaria->delete();

        return redirect()->route('maquinaria.index')
            ->with('success', 'Maquinaria eliminada exitosamente.');
    }

    // =============================================
    // CAMBIO DE ESTADO
    // =============================================

    public function cambiarEstado(Request $request, Maquinaria $maquinaria)
    {
        $validated = $request->validate([
            'estado' => 'required|in:operativa,en_reparacion,baja',
            'motivo' => 'nullable|string|max:500',
        ]);

        $maquinaria->update(['estado' => $validated['estado']]);

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Estado de la maquinaria actualizado.');
    }

    // =============================================
    // ASIGNACIONES
    // =============================================

    public function asignarObra(Request $request, Maquinaria $maquinaria)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'trabajador_id' => 'nullable|exists:trabajadores,id',
            'notas' => 'nullable|string',
        ]);

        // Cerrar asignación anterior si existe
        if ($maquinaria->obra_asignada_id) {
            MaquinariaAsignacion::where('maquinaria_id', $maquinaria->id)
                ->whereNull('fecha_fin')
                ->update(['fecha_fin' => now()]);
        }

        // Crear nueva asignación
        MaquinariaAsignacion::create([
            'maquinaria_id' => $maquinaria->id,
            'obra_id' => $validated['obra_id'],
            'fecha_inicio' => now(),
            'notas' => $validated['notas'] ?? null,
        ]);

        // Actualizar maquinaria
        $maquinaria->update([
            'obra_asignada_id' => $validated['obra_id'],
            'trabajador_asignado_id' => $validated['trabajador_id'] ?? null,
        ]);

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Maquinaria asignada a obra exitosamente.');
    }

    public function desasignarObra(Maquinaria $maquinaria)
    {
        // Cerrar asignación actual
        MaquinariaAsignacion::where('maquinaria_id', $maquinaria->id)
            ->whereNull('fecha_fin')
            ->update(['fecha_fin' => now()]);

        // Limpiar asignación en maquinaria
        $maquinaria->update([
            'obra_asignada_id' => null,
            'trabajador_asignado_id' => null,
        ]);

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Maquinaria desasignada de obra.');
    }

    // =============================================
    // INSPECCIONES
    // =============================================

    public function createInspeccion(Maquinaria $maquinaria)
    {
        $plantillas = MaquinariaChecklistPlantilla::with(['items' => function ($q) {
                $q->orderBy('orden');
            }])
            ->where(function ($q) use ($maquinaria) {
                $q->where('maquinaria_tipo_id', $maquinaria->maquinaria_tipo_id)
                  ->orWhereNull('maquinaria_tipo_id');
            })
            ->where('activa', true)
            ->get();

        return view('maquinaria.inspecciones.create', compact('maquinaria', 'plantillas'));
    }

    public function storeInspeccion(Request $request, Maquinaria $maquinaria)
    {
        $validated = $request->validate([
            'plantilla_id' => 'required|exists:maquinaria_checklist_plantillas,id',
            'fecha' => 'required|date',
            'resultado' => 'required|in:apto,no_apto',
            'observaciones' => 'nullable|string',
            'items' => 'required|array',
            'items.*' => 'required|in:apto,no_apto,no_aplica',
            'items_observaciones' => 'nullable|array',
            'items_observaciones.*' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Crear inspeccion
            $inspeccion = MaquinariaInspeccion::create([
                'maquinaria_id' => $maquinaria->id,
                'plantilla_id' => $validated['plantilla_id'],
                'fecha_inspeccion' => $validated['fecha'],
                'resultado' => $validated['resultado'],
                'observaciones' => $validated['observaciones'] ?? null,
                'realizado_por' => auth()->id(),
            ]);

            // Crear items de inspeccion
            // El formato es items[item_id] = 'apto|no_apto|no_aplica'
            foreach ($validated['items'] as $itemId => $valor) {
                // Convertir valor a booleano cumple
                // apto = true, no_apto = false, no_aplica = null
                $cumple = null;
                if ($valor === 'apto') {
                    $cumple = true;
                } elseif ($valor === 'no_apto') {
                    $cumple = false;
                }
                // no_aplica se guarda como null

                MaquinariaInspeccionItem::create([
                    'inspeccion_id' => $inspeccion->id,
                    'checklist_item_id' => $itemId,
                    'cumple' => $cumple,
                    'observacion' => $request->input("items_observaciones.{$itemId}"),
                ]);
            }

            // Si no apto, cambiar estado a en_reparacion
            if ($validated['resultado'] === 'no_apto') {
                $maquinaria->update(['estado' => 'en_reparacion']);
            }

            DB::commit();

            return redirect()->route('maquinaria.show', $maquinaria)
                ->with('success', 'Inspeccion registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al registrar inspeccion: ' . $e->getMessage()]);
        }
    }

    // =============================================
    // MANTENIMIENTOS
    // =============================================

    public function storeMantenimiento(Request $request, Maquinaria $maquinaria)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:preventivo,correctivo',
            'fecha' => 'required|date',
            'descripcion' => 'required|string',
            'coste' => 'nullable|numeric|min:0',
            'proveedor' => 'nullable|string|max:255',
            'realizado_por' => 'nullable|string|max:255',
            'proxima_revision' => 'nullable|date|after:fecha',
            'documento' => 'nullable|file|max:10240',
        ], [
            'tipo.required' => 'Debe seleccionar el tipo de mantenimiento.',
            'fecha.required' => 'La fecha es obligatoria.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        $documentoPath = null;
        if ($request->hasFile('documento')) {
            $archivo = $request->file('documento');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaCarpeta = 'uploads/maquinaria/' . $maquinaria->id . '/mantenimientos';

            if (!file_exists(public_path($rutaCarpeta))) {
                mkdir(public_path($rutaCarpeta), 0755, true);
            }

            $archivo->move(public_path($rutaCarpeta), $nombreArchivo);
            $documentoPath = $rutaCarpeta . '/' . $nombreArchivo;
        }

        // Convertir strings vacíos a null
        if (empty($validated['coste'])) $validated['coste'] = null;

        MaquinariaMantenimiento::create([
            'maquinaria_id' => $maquinaria->id,
            'tipo' => $validated['tipo'],
            'fecha' => $validated['fecha'],
            'descripcion' => $validated['descripcion'],
            'coste' => $validated['coste'],
            'proveedor' => $validated['proveedor'] ?? null,
            'realizado_por' => $validated['realizado_por'] ?? null,
            'proxima_revision' => $validated['proxima_revision'] ?? null,
            'documento_path' => $documentoPath,
        ]);

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Mantenimiento registrado exitosamente.');
    }

    public function destroyMantenimiento(Maquinaria $maquinaria, MaquinariaMantenimiento $mantenimiento)
    {
        // Eliminar archivo físico
        if ($mantenimiento->documento_path && file_exists(public_path($mantenimiento->documento_path))) {
            unlink(public_path($mantenimiento->documento_path));
        }

        $mantenimiento->delete();

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Mantenimiento eliminado.');
    }

    // =============================================
    // DOCUMENTOS
    // =============================================

    public function storeDocumento(Request $request, Maquinaria $maquinaria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|max:10240',
        ], [
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'archivo.required' => 'Debe subir un archivo.',
            'archivo.max' => 'El archivo no puede superar los 10MB.',
        ]);

        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
        $rutaCarpeta = 'uploads/maquinaria/' . $maquinaria->id . '/documentos';

        if (!file_exists(public_path($rutaCarpeta))) {
            mkdir(public_path($rutaCarpeta), 0755, true);
        }

        $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

        MaquinariaDocumento::create([
            'maquinaria_id' => $maquinaria->id,
            'nombre' => $validated['nombre'],
            'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
            'subido_por' => auth()->id(),
        ]);

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Documento subido exitosamente.');
    }

    public function destroyDocumento(Maquinaria $maquinaria, MaquinariaDocumento $documento)
    {
        if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
            unlink(public_path($documento->archivo_path));
        }

        $documento->delete();

        return redirect()->route('maquinaria.show', $maquinaria)
            ->with('success', 'Documento eliminado.');
    }

    // =============================================
    // HELPERS
    // =============================================

    private function calcularAmortizacionAcumulada(Maquinaria $maquinaria): float
    {
        if (!$maquinaria->fecha_compra || !$maquinaria->amortizacion_dia) {
            return 0;
        }

        $diasDesdeCompra = $maquinaria->fecha_compra->diffInDays(now());
        $amortizacionAcumulada = $maquinaria->amortizacion_dia * $diasDesdeCompra;

        // No puede superar el coste de adquisición
        if ($maquinaria->coste_adquisicion) {
            $amortizacionAcumulada = min($amortizacionAcumulada, $maquinaria->coste_adquisicion);
        }

        return round($amortizacionAcumulada, 2);
    }
}
