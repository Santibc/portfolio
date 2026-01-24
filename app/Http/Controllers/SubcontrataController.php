<?php

namespace App\Http\Controllers;

use App\Models\Subcontrata;
use App\Models\SubcontrataDocumentoCae;
use App\Models\SubcontrataDocumentoObra;
use App\Models\Obra;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class SubcontrataController extends Controller
{
    // Tipos de documentos CAE disponibles
    const TIPOS_DOCUMENTO_CAE = [
        'tc1' => 'TC1 (Modelo de cotización)',
        'tc2' => 'TC2 (Relación nominal de trabajadores)',
        'seguro_rc' => 'Seguro Responsabilidad Civil',
        'seguro_accidentes' => 'Seguro Accidentes',
        'certificado_ss' => 'Certificado al corriente SS',
        'certificado_hacienda' => 'Certificado al corriente Hacienda',
        'rea' => 'REA (Registro Empresa Acreditada)',
        'plan_prevencion' => 'Plan de Prevención',
        'evaluacion_riesgos' => 'Evaluación de Riesgos',
        'planificacion_preventiva' => 'Planificación Actividad Preventiva',
        'formacion_trabajadores' => 'Formación Trabajadores',
        'aptitud_medica' => 'Aptitud Médica',
        'entrega_epis' => 'Entrega EPIs',
        'otro' => 'Otro',
    ];

    // =============================================
    // CRUD BÁSICO
    // =============================================

    public function index(Request $request)
    {
        $query = Subcontrata::withCount(['trabajadores', 'obras', 'documentosCae']);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->buscar($request->search);
        }

        // Filtro por estado activa
        if ($request->filled('activa')) {
            $query->where('activa', $request->activa === '1');
        }

        // Filtro por homologación
        if ($request->filled('homologada')) {
            $query->where('homologada', $request->homologada === '1');
        }

        // Filtro por documentos vencidos
        if ($request->filled('docs_vencidos') && $request->docs_vencidos === '1') {
            $query->whereHas('documentosCae', function ($q) {
                $q->whereNotNull('fecha_caducidad')
                  ->where('fecha_caducidad', '<', now());
            });
        }

        $subcontratas = $query->orderBy('nombre')->get();

        // Estadísticas
        $stats = [
            'total' => Subcontrata::count(),
            'activas' => Subcontrata::activas()->count(),
            'homologadas' => Subcontrata::homologadas()->count(),
            'documentos_vencidos' => SubcontrataDocumentoCae::whereNotNull('fecha_caducidad')
                ->where('fecha_caducidad', '<', now())
                ->count(),
            'documentos_proximos' => SubcontrataDocumentoCae::whereNotNull('fecha_caducidad')
                ->whereBetween('fecha_caducidad', [now(), now()->addDays(30)])
                ->count(),
        ];

        return view('subcontratas.index', compact('subcontratas', 'stats'));
    }

    public function create()
    {
        return view('subcontratas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'cif' => 'nullable|string|max:20|unique:subcontratas,cif',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'persona_contacto' => 'nullable|string|max:150',
            'tarifa_hora' => 'nullable|numeric|min:0',
            'tarifa_dia' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'cif.unique' => 'Este CIF ya está registrado.',
            'email.email' => 'El email no tiene un formato válido.',
        ]);

        // Convertir strings vacíos a null
        $nullableFields = ['razon_social', 'cif', 'direccion', 'telefono', 'email',
                          'persona_contacto', 'tarifa_hora', 'tarifa_dia', 'notas'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $validated['activa'] = true;
        $validated['homologada'] = false;

        $subcontrata = Subcontrata::create($validated);

        // Registrar en auditoría
        Auditoria::registrar('crear', 'subcontratas', $subcontrata->id, null, $subcontrata->toArray());

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Subcontrata creada exitosamente.');
    }

    public function show(Subcontrata $subcontrata)
    {
        $subcontrata->load([
            'trabajadores' => fn($q) => $q->where('activo', true)->orderBy('apellidos'),
            'documentosCae' => fn($q) => $q->orderByRaw('fecha_caducidad IS NULL, fecha_caducidad ASC'),
            'obras' => fn($q) => $q->orderByDesc('pivot_fecha_inicio'),
        ]);

        // Estadísticas
        $stats = [
            'total_trabajadores' => $subcontrata->trabajadores->count(),
            'trabajadores_activos' => $subcontrata->trabajadores->where('activo', true)->count(),
            'obras_total' => $subcontrata->obras->count(),
            'obras_activas' => $subcontrata->obras->where('pivot.activa', true)->count(),
            'documentos_cae' => $subcontrata->documentosCae->count(),
            'documentos_vencidos' => $subcontrata->documentos_cae_vencidos,
            'documentos_proximos' => $subcontrata->documentos_cae_proximos,
        ];

        // Obras disponibles para asignar (no asignadas actualmente)
        $obrasDisponibles = Obra::whereNotIn('id', $subcontrata->obras->pluck('id'))
            ->whereIn('estado', ['aprobada', 'en_curso'])
            ->orderBy('nombre')
            ->get();

        $tiposDocumentoCae = self::TIPOS_DOCUMENTO_CAE;

        return view('subcontratas.show', compact(
            'subcontrata',
            'stats',
            'obrasDisponibles',
            'tiposDocumentoCae'
        ));
    }

    public function edit(Request $request, Subcontrata $subcontrata)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $subcontrata->id,
                'nombre' => $subcontrata->nombre,
                'razon_social' => $subcontrata->razon_social,
                'cif' => $subcontrata->cif,
                'direccion' => $subcontrata->direccion,
                'telefono' => $subcontrata->telefono,
                'email' => $subcontrata->email,
                'persona_contacto' => $subcontrata->persona_contacto,
                'tarifa_hora' => $subcontrata->tarifa_hora,
                'tarifa_dia' => $subcontrata->tarifa_dia,
                'notas' => $subcontrata->notas,
            ]);
        }

        return view('subcontratas.edit', compact('subcontrata'));
    }

    public function update(Request $request, Subcontrata $subcontrata)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'cif' => 'nullable|string|max:20|unique:subcontratas,cif,' . $subcontrata->id,
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'persona_contacto' => 'nullable|string|max:150',
            'tarifa_hora' => 'nullable|numeric|min:0',
            'tarifa_dia' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'cif.unique' => 'Este CIF ya está registrado.',
            'email.email' => 'El email no tiene un formato válido.',
        ]);

        // Convertir strings vacíos a null
        $nullableFields = ['razon_social', 'cif', 'direccion', 'telefono', 'email',
                          'persona_contacto', 'tarifa_hora', 'tarifa_dia', 'notas'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        // Guardar datos anteriores para auditoría
        $datosAnteriores = $subcontrata->toArray();

        $subcontrata->update($validated);

        // Registrar en auditoría
        Auditoria::registrar('editar', 'subcontratas', $subcontrata->id, $datosAnteriores, $subcontrata->fresh()->toArray());

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Subcontrata actualizada exitosamente.');
    }

    public function destroy(Subcontrata $subcontrata)
    {
        // Verificar si tiene trabajadores activos
        if ($subcontrata->trabajadores()->where('activo', true)->exists()) {
            return redirect()->route('subcontratas.show', $subcontrata)
                ->with('error', 'No se puede eliminar la subcontrata porque tiene trabajadores activos asociados.');
        }

        // Eliminar archivos físicos de documentos CAE
        foreach ($subcontrata->documentosCae as $documento) {
            if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
                unlink(public_path($documento->archivo_path));
            }
        }

        // Eliminar archivos de documentos por obra
        foreach ($subcontrata->documentosObra as $documento) {
            if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
                unlink(public_path($documento->archivo_path));
            }
        }

        // Eliminar carpeta si existe
        $carpeta = public_path('uploads/subcontratas/' . $subcontrata->id);
        if (is_dir($carpeta)) {
            $this->eliminarCarpetaRecursivo($carpeta);
        }

        // Registrar en auditoría antes de eliminar
        Auditoria::registrar('eliminar', 'subcontratas', $subcontrata->id, $subcontrata->toArray(), null);

        $subcontrata->delete();

        return redirect()->route('subcontratas.index')
            ->with('success', 'Subcontrata eliminada exitosamente.');
    }

    // =============================================
    // ESTADO Y HOMOLOGACIÓN
    // =============================================

    public function toggleActiva(Subcontrata $subcontrata)
    {
        $subcontrata->update(['activa' => !$subcontrata->activa]);

        $mensaje = $subcontrata->activa ? 'Subcontrata activada.' : 'Subcontrata desactivada.';

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', $mensaje);
    }

    public function toggleHomologada(Request $request, Subcontrata $subcontrata)
    {
        $nuevoEstado = !$subcontrata->homologada;

        $subcontrata->update([
            'homologada' => $nuevoEstado,
            'fecha_homologacion' => $nuevoEstado ? now() : null,
        ]);

        $mensaje = $nuevoEstado
            ? 'Subcontrata marcada como homologada.'
            : 'Se ha retirado la homologación de la subcontrata.';

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', $mensaje);
    }

    // =============================================
    // DOCUMENTOS CAE
    // =============================================

    public function storeDocumentoCae(Request $request, Subcontrata $subcontrata)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:100',
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'fecha_documento' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date|after_or_equal:fecha_documento',
        ], [
            'tipo.required' => 'Debe seleccionar el tipo de documento.',
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.mimes' => 'El archivo debe ser PDF, JPG o PNG.',
            'archivo.max' => 'El archivo no puede superar 10 MB.',
            'fecha_caducidad.after_or_equal' => 'La fecha de caducidad debe ser posterior a la fecha del documento.',
        ]);

        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo->getClientOriginalName());
        $rutaCarpeta = 'uploads/subcontratas/' . $subcontrata->id . '/cae';

        if (!file_exists(public_path($rutaCarpeta))) {
            mkdir(public_path($rutaCarpeta), 0755, true);
        }

        $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

        SubcontrataDocumentoCae::create([
            'subcontrata_id' => $subcontrata->id,
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
            'fecha_documento' => $validated['fecha_documento'] ?? null,
            'fecha_caducidad' => $validated['fecha_caducidad'] ?? null,
            'verificado' => false,
        ]);

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Documento CAE subido exitosamente.');
    }

    public function verificarDocumentoCae(Subcontrata $subcontrata, SubcontrataDocumentoCae $documento)
    {
        // Verificar que el documento pertenece a la subcontrata
        if ($documento->subcontrata_id !== $subcontrata->id) {
            abort(404);
        }

        $documento->update([
            'verificado' => true,
            'verificado_por' => auth()->id(),
        ]);

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Documento marcado como verificado.');
    }

    public function destroyDocumentoCae(Subcontrata $subcontrata, SubcontrataDocumentoCae $documento)
    {
        // Verificar que el documento pertenece a la subcontrata
        if ($documento->subcontrata_id !== $subcontrata->id) {
            abort(404);
        }

        // Eliminar archivo físico
        if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
            unlink(public_path($documento->archivo_path));
        }

        $documento->delete();

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Documento CAE eliminado.');
    }

    // =============================================
    // ASIGNACIÓN A OBRAS
    // =============================================

    public function addObra(Request $request, Subcontrata $subcontrata)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'importe_contratado' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ], [
            'obra_id.required' => 'Debe seleccionar una obra.',
            'obra_id.exists' => 'La obra seleccionada no existe.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior a la de inicio.',
        ]);

        // Verificar si ya está asignada
        if ($subcontrata->obras()->where('obra_id', $validated['obra_id'])->exists()) {
            return redirect()->route('subcontratas.show', $subcontrata)
                ->with('error', 'Esta subcontrata ya está asignada a esa obra.');
        }

        $subcontrata->obras()->attach($validated['obra_id'], [
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'] ?? null,
            'importe_contratado' => $validated['importe_contratado'] ?? null,
            'notas' => $validated['notas'] ?? null,
            'activa' => true,
        ]);

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Subcontrata asignada a la obra exitosamente.');
    }

    public function removeObra(Subcontrata $subcontrata, Obra $obra)
    {
        // Verificar que hay relación
        if (!$subcontrata->obras()->where('obra_id', $obra->id)->exists()) {
            abort(404);
        }

        // Eliminar documentos específicos de esta obra
        $documentosObra = SubcontrataDocumentoObra::where('subcontrata_id', $subcontrata->id)
            ->where('obra_id', $obra->id)
            ->get();

        foreach ($documentosObra as $documento) {
            if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
                unlink(public_path($documento->archivo_path));
            }
            $documento->delete();
        }

        $subcontrata->obras()->detach($obra->id);

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Subcontrata desasignada de la obra.');
    }

    public function updateObraAsignacion(Request $request, Subcontrata $subcontrata, Obra $obra)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'importe_contratado' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
            'activa' => 'boolean',
        ]);

        $subcontrata->obras()->updateExistingPivot($obra->id, [
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'] ?? null,
            'importe_contratado' => $validated['importe_contratado'] ?? null,
            'notas' => $validated['notas'] ?? null,
            'activa' => $validated['activa'] ?? true,
        ]);

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Asignación actualizada.');
    }

    // =============================================
    // DOCUMENTOS POR OBRA
    // =============================================

    public function storeDocumentoObra(Request $request, Subcontrata $subcontrata, Obra $obra)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:100',
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'fecha_documento' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date|after_or_equal:fecha_documento',
            'obligatorio' => 'boolean',
        ]);

        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo->getClientOriginalName());
        $rutaCarpeta = 'uploads/subcontratas/' . $subcontrata->id . '/documentos_obra/obra_' . $obra->id;

        if (!file_exists(public_path($rutaCarpeta))) {
            mkdir(public_path($rutaCarpeta), 0755, true);
        }

        $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

        SubcontrataDocumentoObra::create([
            'subcontrata_id' => $subcontrata->id,
            'obra_id' => $obra->id,
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
            'fecha_documento' => $validated['fecha_documento'] ?? null,
            'fecha_caducidad' => $validated['fecha_caducidad'] ?? null,
            'obligatorio' => $validated['obligatorio'] ?? false,
            'verificado' => false,
        ]);

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Documento subido exitosamente.');
    }

    public function destroyDocumentoObra(Subcontrata $subcontrata, Obra $obra, SubcontrataDocumentoObra $documento)
    {
        // Verificar que el documento pertenece a la subcontrata y obra
        if ($documento->subcontrata_id !== $subcontrata->id || $documento->obra_id !== $obra->id) {
            abort(404);
        }

        // Eliminar archivo físico
        if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
            unlink(public_path($documento->archivo_path));
        }

        $documento->delete();

        return redirect()->route('subcontratas.show', $subcontrata)
            ->with('success', 'Documento eliminado.');
    }

    // =============================================
    // HELPERS
    // =============================================

    private function eliminarCarpetaRecursivo($carpeta)
    {
        if (!is_dir($carpeta)) {
            return;
        }

        $archivos = array_diff(scandir($carpeta), ['.', '..']);
        foreach ($archivos as $archivo) {
            $ruta = $carpeta . DIRECTORY_SEPARATOR . $archivo;
            if (is_dir($ruta)) {
                $this->eliminarCarpetaRecursivo($ruta);
            } else {
                unlink($ruta);
            }
        }
        rmdir($carpeta);
    }
}
