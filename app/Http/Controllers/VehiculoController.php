<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\VehiculoTipo;
use App\Models\VehiculoDocumento;
use App\Models\Trabajador;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehiculo::with(['tipo', 'conductorHabitual'])
            ->withCount('documentos');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('matricula', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('numero_bastidor', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('vehiculo_tipo_id')) {
            $query->where('vehiculo_tipo_id', $request->vehiculo_tipo_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por ITV próxima (30 días)
        if ($request->filled('itv_proxima') && $request->itv_proxima === '1') {
            $query->conItvProxima(30);
        }

        // Filtro por seguro próximo (45 días)
        if ($request->filled('seguro_proximo') && $request->seguro_proximo === '1') {
            $query->conSeguroProximo(45);
        }

        $vehiculos = $query->orderBy('matricula')->get();

        // Datos para filtros
        $tipos = VehiculoTipo::orderBy('nombre')->get();

        // Estadísticas
        $stats = [
            'total' => Vehiculo::count(),
            'operativos' => Vehiculo::where('estado', 'operativo')->count(),
            'en_taller' => Vehiculo::where('estado', 'en_taller')->count(),
            'baja' => Vehiculo::where('estado', 'baja')->count(),
            'valor_total' => Vehiculo::sum('coste_adquisicion'),
            'itv_proxima' => Vehiculo::conItvProxima(30)->count(),
            'seguro_proximo' => Vehiculo::conSeguroProximo(45)->count(),
        ];

        return view('vehiculos.index', compact('vehiculos', 'tipos', 'stats'));
    }

    public function create()
    {
        $tipos = VehiculoTipo::orderBy('nombre')->get();
        $conductores = Trabajador::where('activo', true)->orderBy('apellidos')->get();

        return view('vehiculos.create', compact('tipos', 'conductores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehiculo_tipo_id' => 'required|exists:vehiculo_tipos,id',
            'matricula' => 'required|string|max:20|unique:vehiculos',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_bastidor' => 'nullable|string|max:100',
            'fecha_matriculacion' => 'nullable|date',
            'fecha_compra' => 'nullable|date',
            'fecha_ultima_itv' => 'nullable|date',
            'fecha_proxima_itv' => 'nullable|date|after_or_equal:fecha_ultima_itv',
            'compania_seguro' => 'nullable|string|max:150',
            'numero_poliza' => 'nullable|string|max:100',
            'fecha_vencimiento_seguro' => 'nullable|date',
            'coste_adquisicion' => 'nullable|numeric|min:0',
            'coste_dia' => 'nullable|numeric|min:0',
            'kilometraje_actual' => 'nullable|integer|min:0',
            'conductor_habitual_id' => 'nullable|exists:trabajadores,id',
            'notas' => 'nullable|string',
        ], [
            'vehiculo_tipo_id.required' => 'Debe seleccionar un tipo de vehículo.',
            'vehiculo_tipo_id.exists' => 'El tipo de vehículo seleccionado no existe.',
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.unique' => 'Esta matrícula ya está registrada.',
            'fecha_proxima_itv.after_or_equal' => 'La fecha de próxima ITV debe ser posterior a la última ITV.',
        ]);

        // Convertir strings vacíos a null
        $nullableFields = ['marca', 'modelo', 'numero_bastidor', 'fecha_matriculacion', 'fecha_compra',
                          'fecha_ultima_itv', 'fecha_proxima_itv', 'compania_seguro', 'numero_poliza',
                          'fecha_vencimiento_seguro', 'coste_adquisicion', 'coste_dia', 'kilometraje_actual',
                          'conductor_habitual_id', 'notas'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $validated['estado'] = 'operativo';

        $vehiculo = Vehiculo::create($validated);

        // Registrar en auditoría
        Auditoria::registrar('crear', 'vehiculos', $vehiculo->id, null, $vehiculo->toArray());

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo registrado exitosamente.');
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load(['tipo', 'conductorHabitual', 'documentos']);

        $conductoresDisponibles = Trabajador::where('activo', true)
            ->orderBy('apellidos')
            ->get();

        // Estadísticas
        $stats = [
            'total_documentos' => $vehiculo->documentos->count(),
            'itv_status' => $vehiculo->itv_status,
            'seguro_status' => $vehiculo->seguro_status,
            'dias_hasta_itv' => $vehiculo->fecha_proxima_itv
                ? now()->diffInDays($vehiculo->fecha_proxima_itv, false)
                : null,
            'dias_hasta_seguro' => $vehiculo->fecha_vencimiento_seguro
                ? now()->diffInDays($vehiculo->fecha_vencimiento_seguro, false)
                : null,
        ];

        return view('vehiculos.show', compact('vehiculo', 'conductoresDisponibles', 'stats'));
    }

    public function edit(Request $request, Vehiculo $vehiculo)
    {
        // Si es petición AJAX, devolver JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $vehiculo->id,
                'vehiculo_tipo_id' => $vehiculo->vehiculo_tipo_id,
                'matricula' => $vehiculo->matricula,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'numero_bastidor' => $vehiculo->numero_bastidor,
                'fecha_matriculacion' => $vehiculo->fecha_matriculacion?->format('Y-m-d'),
                'fecha_compra' => $vehiculo->fecha_compra?->format('Y-m-d'),
                'fecha_ultima_itv' => $vehiculo->fecha_ultima_itv?->format('Y-m-d'),
                'fecha_proxima_itv' => $vehiculo->fecha_proxima_itv?->format('Y-m-d'),
                'compania_seguro' => $vehiculo->compania_seguro,
                'numero_poliza' => $vehiculo->numero_poliza,
                'fecha_vencimiento_seguro' => $vehiculo->fecha_vencimiento_seguro?->format('Y-m-d'),
                'coste_adquisicion' => $vehiculo->coste_adquisicion,
                'coste_dia' => $vehiculo->coste_dia,
                'estado' => $vehiculo->estado,
                'kilometraje_actual' => $vehiculo->kilometraje_actual,
                'conductor_habitual_id' => $vehiculo->conductor_habitual_id,
                'notas' => $vehiculo->notas,
            ]);
        }

        $tipos = VehiculoTipo::orderBy('nombre')->get();
        $conductores = Trabajador::where('activo', true)->orderBy('apellidos')->get();

        return view('vehiculos.edit', compact('vehiculo', 'tipos', 'conductores'));
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'vehiculo_tipo_id' => 'required|exists:vehiculo_tipos,id',
            'matricula' => 'required|string|max:20|unique:vehiculos,matricula,' . $vehiculo->id,
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'numero_bastidor' => 'nullable|string|max:100',
            'fecha_matriculacion' => 'nullable|date',
            'fecha_compra' => 'nullable|date',
            'fecha_ultima_itv' => 'nullable|date',
            'fecha_proxima_itv' => 'nullable|date|after_or_equal:fecha_ultima_itv',
            'compania_seguro' => 'nullable|string|max:150',
            'numero_poliza' => 'nullable|string|max:100',
            'fecha_vencimiento_seguro' => 'nullable|date',
            'coste_adquisicion' => 'nullable|numeric|min:0',
            'coste_dia' => 'nullable|numeric|min:0',
            'estado' => 'required|in:operativo,en_taller,baja',
            'kilometraje_actual' => 'nullable|integer|min:0',
            'conductor_habitual_id' => 'nullable|exists:trabajadores,id',
            'notas' => 'nullable|string',
        ], [
            'vehiculo_tipo_id.required' => 'Debe seleccionar un tipo de vehículo.',
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.unique' => 'Esta matrícula ya está registrada.',
        ]);

        // Convertir strings vacíos a null
        $nullableFields = ['marca', 'modelo', 'numero_bastidor', 'fecha_matriculacion', 'fecha_compra',
                          'fecha_ultima_itv', 'fecha_proxima_itv', 'compania_seguro', 'numero_poliza',
                          'fecha_vencimiento_seguro', 'coste_adquisicion', 'coste_dia', 'kilometraje_actual',
                          'conductor_habitual_id', 'notas'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        // Guardar datos anteriores para auditoría
        $datosAnteriores = $vehiculo->toArray();

        $vehiculo->update($validated);

        // Registrar en auditoría
        Auditoria::registrar('editar', 'vehiculos', $vehiculo->id, $datosAnteriores, $vehiculo->fresh()->toArray());

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo actualizado exitosamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        // Registrar en auditoría antes de eliminar
        Auditoria::registrar('eliminar', 'vehiculos', $vehiculo->id, $vehiculo->toArray(), null);

        // Eliminar archivos físicos de documentos
        foreach ($vehiculo->documentos as $documento) {
            if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
                unlink(public_path($documento->archivo_path));
            }
        }

        // Eliminar carpeta si está vacía
        $carpeta = public_path('uploads/vehiculos/' . $vehiculo->id);
        if (is_dir($carpeta)) {
            $this->eliminarCarpetaRecursivo($carpeta);
        }

        $vehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }

    // =============================================
    // CAMBIO DE ESTADO
    // =============================================

    public function cambiarEstado(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'estado' => 'required|in:operativo,en_taller,baja',
        ]);

        $vehiculo->update(['estado' => $validated['estado']]);

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', 'Estado del vehículo actualizado.');
    }

    // =============================================
    // DOCUMENTOS
    // =============================================

    public function storeDocumento(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:ficha_tecnica,permiso_circulacion,seguro,itv,otro',
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|file|max:10240',
            'fecha_documento' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date|after_or_equal:fecha_documento',
        ], [
            'tipo.required' => 'Debe seleccionar el tipo de documento.',
            'nombre.required' => 'El nombre del documento es obligatorio.',
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.max' => 'El archivo no puede superar 10 MB.',
        ]);

        $archivo = $request->file('archivo');
        $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
        $rutaCarpeta = 'uploads/vehiculos/' . $vehiculo->id . '/documentos';

        if (!file_exists(public_path($rutaCarpeta))) {
            mkdir(public_path($rutaCarpeta), 0755, true);
        }

        $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

        VehiculoDocumento::create([
            'vehiculo_id' => $vehiculo->id,
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'archivo_path' => $rutaCarpeta . '/' . $nombreArchivo,
            'fecha_documento' => $validated['fecha_documento'] ?? null,
            'fecha_caducidad' => $validated['fecha_caducidad'] ?? null,
        ]);

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', 'Documento subido exitosamente.');
    }

    public function destroyDocumento(Vehiculo $vehiculo, VehiculoDocumento $documento)
    {
        // Verificar que el documento pertenece al vehículo
        if ($documento->vehiculo_id !== $vehiculo->id) {
            abort(404);
        }

        // Eliminar archivo físico
        if ($documento->archivo_path && file_exists(public_path($documento->archivo_path))) {
            unlink(public_path($documento->archivo_path));
        }

        $documento->delete();

        return redirect()->route('vehiculos.show', $vehiculo)
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
