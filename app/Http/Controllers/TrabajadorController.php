<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Subcontrata;
use App\Models\Cuadrilla;
use App\Models\TrabajadorDocumento;
use App\Models\TrabajadorFormacion;
use App\Models\FormacionTipo;
use App\Models\TrabajadorHistorialDisciplinario;
use App\Models\Auditoria;
use App\Models\EmailLog;
use App\Notifications\DocumentoTrabajadorNotification;
use App\Notifications\BienvenidaTrabajadorNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TrabajadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Trabajador::with(['subcontrata', 'cuadrillas']);

        // Filtro por búsqueda (nombre, apellidos, DNI)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo de relación
        if ($request->filled('tipo_relacion')) {
            $query->where('tipo_relacion', $request->tipo_relacion);
        }

        // Filtro por estado
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        // Filtro por subcontrata
        if ($request->filled('subcontrata_id')) {
            $query->where('subcontrata_id', $request->subcontrata_id);
        }

        // Filtro por cuadrilla
        if ($request->filled('cuadrilla_id')) {
            $query->whereHas('cuadrillas', function ($q) use ($request) {
                $q->where('cuadrillas.id', $request->cuadrilla_id)
                  ->wherePivot('activo', true);
            });
        }

        $trabajadores = $query->orderBy('apellidos')->orderBy('nombre')->get();
        $subcontratas = Subcontrata::where('activa', true)->orderBy('nombre')->get();
        $cuadrillas = Cuadrilla::where('activa', true)->orderBy('nombre')->get();

        return view('trabajadores.index', compact('trabajadores', 'subcontratas', 'cuadrillas'));
    }

    public function create()
    {
        $subcontratas = Subcontrata::where('activa', true)->orderBy('nombre')->get();
        $cuadrillas = Cuadrilla::where('activa', true)->orderBy('nombre')->get();

        return view('trabajadores.create', compact('subcontratas', 'cuadrillas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_relacion' => 'required|in:propio,subcontrata',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:150',
            'dni' => 'required|string|max:20|unique:trabajadores',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_alta' => 'required|date',
            'categoria_convenio' => 'nullable|string|max:100',
            'salario_bruto_mensual' => 'nullable|numeric|min:0',
            'coste_empresa_dia' => 'nullable|numeric|min:0',
            'coste_hora' => 'nullable|numeric|min:0',
            'vacaciones_anuales' => 'nullable|integer|min:0',
            'subcontrata_id' => 'nullable|exists:subcontratas,id',
            'cuadrilla_id' => 'nullable|exists:cuadrillas,id',
        ], [
            'tipo_relacion.required' => 'El tipo de relación es obligatorio.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'fecha_alta.required' => 'La fecha de alta es obligatoria.',
            'subcontrata_id.exists' => 'La subcontrata seleccionada no existe.',
        ]);

        // Si es de subcontrata, debe tener subcontrata_id
        if ($validated['tipo_relacion'] === 'subcontrata' && empty($validated['subcontrata_id'])) {
            return back()->withErrors(['subcontrata_id' => 'Debe seleccionar una subcontrata.'])->withInput();
        }

        // Si es propio, no debe tener subcontrata
        if ($validated['tipo_relacion'] === 'propio') {
            $validated['subcontrata_id'] = null;
        }

        $validated['activo'] = true;
        $validated['vacaciones_acumuladas'] = 0;
        $validated['antiguedad'] = $validated['fecha_alta'];

        $trabajador = Trabajador::create($validated);

        // Registrar en auditoría
        Auditoria::registrar('crear', 'trabajadores', $trabajador->id, null, $trabajador->toArray());

        // Asignar a cuadrilla si se especificó
        if ($request->filled('cuadrilla_id')) {
            $trabajador->cuadrillas()->attach($request->cuadrilla_id, [
                'fecha_incorporacion' => now(),
                'activo' => true,
            ]);
        }

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador creado exitosamente.');
    }

    public function show(Trabajador $trabajador)
    {
        $trabajador->load([
            'subcontrata',
            'cuadrillas',
            'documentos',
            'formaciones.tipo',
            'historialDisciplinario',
            'episEntregados.inventario.catalogo',
        ]);

        $formacionTipos = FormacionTipo::orderBy('nombre')->get();

        return view('trabajadores.show', compact('trabajador', 'formacionTipos'));
    }

    public function edit(Request $request, Trabajador $trabajador)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $trabajador->id,
                'tipo_relacion' => $trabajador->tipo_relacion,
                'nombre' => $trabajador->nombre,
                'apellidos' => $trabajador->apellidos,
                'dni' => $trabajador->dni,
                'email' => $trabajador->email,
                'telefono' => $trabajador->telefono,
                'direccion' => $trabajador->direccion,
                'fecha_nacimiento' => $trabajador->fecha_nacimiento?->format('Y-m-d'),
                'fecha_alta' => $trabajador->fecha_alta?->format('Y-m-d'),
                'categoria_convenio' => $trabajador->categoria_convenio,
                'salario_bruto_mensual' => $trabajador->salario_bruto_mensual,
                'coste_empresa_dia' => $trabajador->coste_empresa_dia,
                'coste_hora' => $trabajador->coste_hora,
                'vacaciones_anuales' => $trabajador->vacaciones_anuales,
                'subcontrata_id' => $trabajador->subcontrata_id,
                'cuadrilla_id' => $trabajador->cuadrillaActual()?->id,
                'activo' => $trabajador->activo,
            ]);
        }

        $subcontratas = Subcontrata::where('activa', true)->orderBy('nombre')->get();
        $cuadrillas = Cuadrilla::where('activa', true)->orderBy('nombre')->get();

        return view('trabajadores.edit', compact('trabajador', 'subcontratas', 'cuadrillas'));
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'tipo_relacion' => 'required|in:propio,subcontrata',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:150',
            'dni' => 'required|string|max:20|unique:trabajadores,dni,' . $trabajador->id,
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_alta' => 'required|date',
            'fecha_baja' => 'nullable|date|after_or_equal:fecha_alta',
            'categoria_convenio' => 'nullable|string|max:100',
            'salario_bruto_mensual' => 'nullable|numeric|min:0',
            'coste_empresa_dia' => 'nullable|numeric|min:0',
            'coste_hora' => 'nullable|numeric|min:0',
            'vacaciones_anuales' => 'nullable|integer|min:0',
            'subcontrata_id' => 'nullable|exists:subcontratas,id',
            'cuadrilla_id' => 'nullable|exists:cuadrillas,id',
            'activo' => 'boolean',
        ], [
            'tipo_relacion.required' => 'El tipo de relación es obligatorio.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'fecha_alta.required' => 'La fecha de alta es obligatoria.',
        ]);

        // Si es de subcontrata, debe tener subcontrata_id
        if ($validated['tipo_relacion'] === 'subcontrata' && empty($validated['subcontrata_id'])) {
            return back()->withErrors(['subcontrata_id' => 'Debe seleccionar una subcontrata.'])->withInput();
        }

        // Si es propio, no debe tener subcontrata
        if ($validated['tipo_relacion'] === 'propio') {
            $validated['subcontrata_id'] = null;
        }

        $validated['activo'] = $request->boolean('activo', true);

        // Guardar datos anteriores para auditoría
        $datosAnteriores = $trabajador->toArray();

        $trabajador->update($validated);

        // Registrar en auditoría
        Auditoria::registrar('editar', 'trabajadores', $trabajador->id, $datosAnteriores, $trabajador->fresh()->toArray());

        // Gestionar cuadrilla
        $cuadrillaActual = $trabajador->cuadrillaActual();
        $nuevaCuadrillaId = $request->cuadrilla_id;

        if ($cuadrillaActual?->id != $nuevaCuadrillaId) {
            // Desactivar cuadrilla actual
            if ($cuadrillaActual) {
                $trabajador->cuadrillas()->updateExistingPivot($cuadrillaActual->id, [
                    'activo' => false,
                    'fecha_salida' => now(),
                ]);
            }

            // Activar nueva cuadrilla
            if ($nuevaCuadrillaId) {
                $existente = $trabajador->cuadrillas()->where('cuadrillas.id', $nuevaCuadrillaId)->first();
                if ($existente) {
                    $trabajador->cuadrillas()->updateExistingPivot($nuevaCuadrillaId, [
                        'activo' => true,
                        'fecha_salida' => null,
                    ]);
                } else {
                    $trabajador->cuadrillas()->attach($nuevaCuadrillaId, [
                        'fecha_incorporacion' => now(),
                        'activo' => true,
                    ]);
                }
            }
        }

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador actualizado exitosamente.');
    }

    public function destroy(Trabajador $trabajador)
    {
        // Verificar que no tiene fichajes activos u obras activas
        if ($trabajador->fichajes()->whereNull('hora_salida')->exists()) {
            return redirect()->route('trabajadores.index')
                ->with('error', 'No se puede eliminar un trabajador con fichajes activos.');
        }

        // Registrar en auditoría antes de eliminar
        Auditoria::registrar('eliminar', 'trabajadores', $trabajador->id, $trabajador->toArray(), null);

        $trabajador->delete();

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador eliminado exitosamente.');
    }

    // =============================================
    // DOCUMENTOS
    // =============================================

    public function storeDocumento(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50',
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'fecha_documento' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date',
            'visible_trabajador' => 'boolean',
            'requiere_lectura' => 'boolean',
        ], [
            'tipo.required' => 'El tipo de documento es obligatorio.',
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'archivo.required' => 'Debe subir un archivo.',
            'archivo.mimes' => 'El archivo debe ser PDF, JPG o PNG.',
            'archivo.max' => 'El archivo no puede superar los 10MB.',
        ]);

        // Guardar archivo en public/uploads
        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
        $rutaCarpeta = 'uploads/trabajadores/' . $trabajador->id . '/documentos';
        $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

        $documento = TrabajadorDocumento::create([
            'trabajador_id' => $trabajador->id,
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
            'fecha_documento' => $validated['fecha_documento'] ?? now(),
            'fecha_caducidad' => $validated['fecha_caducidad'],
            'visible_trabajador' => $request->boolean('visible_trabajador', true),
            'requiere_lectura' => $request->boolean('requiere_lectura', false),
        ]);

        // Enviar notificación por email si el documento es visible para el trabajador
        if ($documento->visible_trabajador && $trabajador->user) {
            try {
                $trabajador->user->notify(new DocumentoTrabajadorNotification($documento));

                EmailLog::logEnviado(
                    EmailLog::TIPO_DOCUMENTO,
                    $trabajador->user->email,
                    "Nuevo documento disponible - {$documento->nombre}",
                    $documento,
                    $trabajador->user->id
                );
            } catch (\Exception $e) {
                Log::error("Error enviando notificación de documento", [
                    'documento_id' => $documento->id,
                    'trabajador_id' => $trabajador->id,
                    'error' => $e->getMessage()
                ]);

                EmailLog::logFallido(
                    EmailLog::TIPO_DOCUMENTO,
                    $trabajador->user->email,
                    "Nuevo documento disponible - {$documento->nombre}",
                    $e->getMessage(),
                    $documento,
                    $trabajador->user->id
                );
            }
        }

        return redirect()->route('trabajadores.show', $trabajador)
            ->with('success', 'Documento subido exitosamente.');
    }

    public function destroyDocumento(Trabajador $trabajador, TrabajadorDocumento $documento)
    {
        // Eliminar archivo físico
        if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
            unlink(public_path($documento->archivo_path));
        }

        $documento->delete();

        return redirect()->route('trabajadores.show', $trabajador)
            ->with('success', 'Documento eliminado exitosamente.');
    }

    // =============================================
    // FORMACIONES
    // =============================================

    public function storeFormacion(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'formacion_tipo_id' => 'required|exists:formacion_tipos,id',
            'fecha_realizacion' => 'required|date',
            'fecha_caducidad' => 'nullable|date|after:fecha_realizacion',
            'centro_formacion' => 'nullable|string|max:255',
            'certificado' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notas' => 'nullable|string',
        ], [
            'formacion_tipo_id.required' => 'Debe seleccionar un tipo de formación.',
            'fecha_realizacion.required' => 'La fecha de realización es obligatoria.',
            'certificado.mimes' => 'El certificado debe ser PDF, JPG o PNG.',
            'certificado.max' => 'El certificado no puede superar los 10MB.',
        ]);

        $certificadoPath = null;
        if ($request->hasFile('certificado')) {
            $archivo = $request->file('certificado');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaCarpeta = 'uploads/trabajadores/' . $trabajador->id . '/formaciones';
            $archivo->move(public_path($rutaCarpeta), $nombreArchivo);
            $certificadoPath = $rutaCarpeta . '/' . $nombreArchivo;
        }

        TrabajadorFormacion::create([
            'trabajador_id' => $trabajador->id,
            'formacion_tipo_id' => $validated['formacion_tipo_id'],
            'fecha_realizacion' => $validated['fecha_realizacion'],
            'fecha_caducidad' => $validated['fecha_caducidad'],
            'centro_formacion' => $validated['centro_formacion'],
            'certificado_path' => $certificadoPath,
            'notas' => $validated['notas'],
        ]);

        return redirect()->route('trabajadores.show', $trabajador)
            ->with('success', 'Formación registrada exitosamente.');
    }

    public function destroyFormacion(Trabajador $trabajador, TrabajadorFormacion $formacion)
    {
        // Eliminar archivo físico
        if ($formacion->certificado_path && file_exists(public_path($formacion->certificado_path))) {
            unlink(public_path($formacion->certificado_path));
        }

        $formacion->delete();

        return redirect()->route('trabajadores.show', $trabajador)
            ->with('success', 'Formación eliminada exitosamente.');
    }

    // =============================================
    // HISTORIAL DISCIPLINARIO
    // =============================================

    public function storeHistorial(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:amonestacion_verbal,amonestacion_escrita,sancion_leve,sancion_grave,sancion_muy_grave',
            'fecha' => 'required|date',
            'descripcion' => 'required|string',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'tipo.required' => 'El tipo de incidencia es obligatorio.',
            'tipo.in' => 'El tipo de incidencia seleccionado no es válido.',
            'fecha.required' => 'La fecha es obligatoria.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ]);

        $documentoPath = null;
        if ($request->hasFile('documento')) {
            $archivo = $request->file('documento');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $rutaCarpeta = 'uploads/trabajadores/' . $trabajador->id . '/historial';
            $archivo->move(public_path($rutaCarpeta), $nombreArchivo);
            $documentoPath = $rutaCarpeta . '/' . $nombreArchivo;
        }

        TrabajadorHistorialDisciplinario::create([
            'trabajador_id' => $trabajador->id,
            'tipo' => $validated['tipo'],
            'fecha' => $validated['fecha'],
            'descripcion' => $validated['descripcion'],
            'documento_path' => $documentoPath,
            'registrado_por' => auth()->id(),
        ]);

        return redirect()->route('trabajadores.show', $trabajador)
            ->with('success', 'Incidencia registrada exitosamente.');
    }

    // =============================================
    // BAJA DE TRABAJADOR
    // =============================================

    public function darBaja(Request $request, Trabajador $trabajador)
    {
        $validated = $request->validate([
            'fecha_baja' => 'required|date|after_or_equal:' . $trabajador->fecha_alta->format('Y-m-d'),
        ], [
            'fecha_baja.required' => 'La fecha de baja es obligatoria.',
            'fecha_baja.after_or_equal' => 'La fecha de baja debe ser posterior a la fecha de alta.',
        ]);

        $trabajador->update([
            'fecha_baja' => $validated['fecha_baja'],
            'activo' => false,
        ]);

        // Desactivar de todas las cuadrillas
        $trabajador->cuadrillas()->wherePivot('activo', true)->update([
            'cuadrilla_trabajadores.activo' => false,
            'cuadrilla_trabajadores.fecha_salida' => $validated['fecha_baja'],
        ]);

        return redirect()->route('trabajadores.show', $trabajador)
            ->with('success', 'Trabajador dado de baja exitosamente.');
    }
}
