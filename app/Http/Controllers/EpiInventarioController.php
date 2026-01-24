<?php

namespace App\Http\Controllers;

use App\Models\EpiCatalogo;
use App\Models\EpiInventario;
use App\Models\EpiEntrega;
use App\Models\EpiRevision;
use App\Models\Trabajador;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EpiInventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = EpiInventario::with(['catalogo', 'entregas' => function ($q) {
            $q->whereNull('fecha_devolucion')->with('trabajador');
        }]);

        // Filtro por busqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_serie', 'like', "%{$search}%")
                  ->orWhereHas('catalogo', function ($q2) use ($search) {
                      $q2->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por tipo de EPI (catalogo)
        if ($request->filled('epi_catalogo_id')) {
            $query->where('epi_catalogo_id', $request->epi_catalogo_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por proximos a caducar (30 dias)
        if ($request->filled('proximos_caducar') && $request->proximos_caducar === '1') {
            $query->whereNotNull('fecha_caducidad')
                  ->where('fecha_caducidad', '<=', now()->addDays(30))
                  ->where('estado', '!=', 'baja');
        }

        $inventario = $query->orderBy('created_at', 'desc')->get();

        // Si se solicita formato JSON (para AJAX)
        if ($request->format === 'json') {
            return response()->json([
                'inventario' => $inventario->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'numero_serie' => $item->numero_serie,
                        'estado' => $item->estado,
                        'fecha_caducidad' => $item->fecha_caducidad?->format('d/m/Y'),
                        'catalogo' => $item->catalogo ? [
                            'id' => $item->catalogo->id,
                            'nombre' => $item->catalogo->nombre,
                            'categoria' => $item->catalogo->categoria,
                        ] : null,
                    ];
                }),
            ]);
        }

        // Datos para filtros
        $catalogos = EpiCatalogo::orderBy('categoria')->orderBy('nombre')->get();

        // Estadisticas
        $stats = [
            'total' => EpiInventario::count(),
            'disponibles' => EpiInventario::where('estado', 'disponible')->count(),
            'asignados' => EpiInventario::where('estado', 'asignado')->count(),
            'en_revision' => EpiInventario::where('estado', 'en_revision')->count(),
            'baja' => EpiInventario::where('estado', 'baja')->count(),
            'valor_total' => EpiInventario::where('estado', '!=', 'baja')->sum('coste'),
            'proximos_caducar' => EpiInventario::whereNotNull('fecha_caducidad')
                ->where('fecha_caducidad', '<=', now()->addDays(30))
                ->where('estado', '!=', 'baja')
                ->count(),
        ];

        return view('epis.inventario.index', compact('inventario', 'catalogos', 'stats'));
    }

    public function create()
    {
        $catalogos = EpiCatalogo::orderBy('categoria')->orderBy('nombre')->get();

        return view('epis.inventario.create', compact('catalogos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'epi_catalogo_id' => 'required|exists:epi_catalogo,id',
            'numero_serie' => 'nullable|string|max:100',
            'fecha_compra' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date|after_or_equal:fecha_compra',
            'coste' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ], [
            'epi_catalogo_id.required' => 'Debe seleccionar un tipo de EPI.',
            'fecha_caducidad.after_or_equal' => 'La fecha de caducidad debe ser posterior a la fecha de compra.',
        ]);

        // Estado inicial: disponible
        $validated['estado'] = 'disponible';

        // Convertir strings vacios a null
        $nullableFields = ['numero_serie', 'fecha_compra', 'fecha_caducidad', 'coste', 'notas'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $epi = EpiInventario::create($validated);

        // Registrar en auditoría
        Auditoria::registrar('crear', 'epi_inventario', $epi->id, null, $epi->toArray());

        return redirect()->route('epi-inventario.show', $epi)
            ->with('success', 'EPI registrado exitosamente.');
    }

    public function show(EpiInventario $epiInventario)
    {
        $epiInventario->load([
            'catalogo',
            'entregas.trabajador',
            'entregas.entregadoPor',
            'revisiones.realizadoPor',
        ]);

        // Obtener trabajadores activos para el select de entrega
        $trabajadores = Trabajador::where('activo', true)
            ->orderBy('apellidos')
            ->get();

        // Entrega actual (si existe)
        $entregaActual = $epiInventario->entregaActual();

        return view('epis.inventario.show', compact('epiInventario', 'trabajadores', 'entregaActual'));
    }

    public function edit(EpiInventario $epiInventario)
    {
        $catalogos = EpiCatalogo::orderBy('categoria')->orderBy('nombre')->get();

        return view('epis.inventario.edit', compact('epiInventario', 'catalogos'));
    }

    public function update(Request $request, EpiInventario $epiInventario)
    {
        $validated = $request->validate([
            'epi_catalogo_id' => 'required|exists:epi_catalogo,id',
            'numero_serie' => 'nullable|string|max:100',
            'fecha_compra' => 'nullable|date',
            'fecha_caducidad' => 'nullable|date|after_or_equal:fecha_compra',
            'coste' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ], [
            'epi_catalogo_id.required' => 'Debe seleccionar un tipo de EPI.',
            'fecha_caducidad.after_or_equal' => 'La fecha de caducidad debe ser posterior a la fecha de compra.',
        ]);

        // Convertir strings vacios a null
        $nullableFields = ['numero_serie', 'fecha_compra', 'fecha_caducidad', 'coste', 'notas'];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        // Guardar datos anteriores para auditoría
        $datosAnteriores = $epiInventario->toArray();

        $epiInventario->update($validated);

        // Registrar en auditoría
        Auditoria::registrar('editar', 'epi_inventario', $epiInventario->id, $datosAnteriores, $epiInventario->fresh()->toArray());

        return redirect()->route('epi-inventario.show', $epiInventario)
            ->with('success', 'EPI actualizado exitosamente.');
    }

    public function destroy(EpiInventario $epiInventario)
    {
        // Solo se puede eliminar si esta disponible y no tiene entregas
        if ($epiInventario->estado !== 'disponible') {
            return redirect()->route('epi-inventario.index')
                ->with('error', 'Solo se pueden eliminar EPIs en estado disponible.');
        }

        if ($epiInventario->entregas()->count() > 0) {
            return redirect()->route('epi-inventario.index')
                ->with('error', 'No se puede eliminar un EPI que ha sido entregado anteriormente.');
        }

        // Registrar en auditoría antes de eliminar
        Auditoria::registrar('eliminar', 'epi_inventario', $epiInventario->id, $epiInventario->toArray(), null);

        $epiInventario->delete();

        return redirect()->route('epi-inventario.index')
            ->with('success', 'EPI eliminado exitosamente.');
    }

    /**
     * Entregar EPI a un trabajador
     */
    public function entregarEpi(Request $request, EpiInventario $epiInventario)
    {
        // Verificar que el EPI esta disponible
        if ($epiInventario->estado !== 'disponible') {
            return redirect()->back()
                ->with('error', 'Este EPI no esta disponible para entregar.');
        }

        $validated = $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'fecha_entrega' => 'required|date|before_or_equal:today',
            'firma' => 'required|string', // Base64 de la firma
        ], [
            'trabajador_id.required' => 'Debe seleccionar un trabajador.',
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.before_or_equal' => 'La fecha de entrega no puede ser futura.',
            'firma.required' => 'La firma del trabajador es obligatoria.',
        ]);

        DB::beginTransaction();
        try {
            // Guardar la firma como imagen
            $firmaPath = null;
            if (!empty($validated['firma'])) {
                $firmaPath = $this->guardarFirma($validated['firma'], $validated['trabajador_id']);
            }

            // Crear la entrega
            EpiEntrega::create([
                'epi_inventario_id' => $epiInventario->id,
                'trabajador_id' => $validated['trabajador_id'],
                'fecha_entrega' => $validated['fecha_entrega'],
                'firma_trabajador_path' => $firmaPath,
                'entregado_por' => Auth::id(),
            ]);

            // Cambiar estado del EPI a asignado
            $epiInventario->update(['estado' => 'asignado']);

            DB::commit();

            return redirect()->route('epi-inventario.show', $epiInventario)
                ->with('success', 'EPI entregado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la entrega: ' . $e->getMessage());
        }
    }

    /**
     * Devolver EPI
     */
    public function devolverEpi(Request $request, EpiInventario $epiInventario)
    {
        // Verificar que el EPI esta asignado
        if ($epiInventario->estado !== 'asignado') {
            return redirect()->back()
                ->with('error', 'Este EPI no esta asignado a ningun trabajador.');
        }

        // Obtener la entrega actual
        $entregaActual = $epiInventario->entregaActual();
        if (!$entregaActual) {
            return redirect()->back()
                ->with('error', 'No se encontro la entrega activa para este EPI.');
        }

        $validated = $request->validate([
            'fecha_devolucion' => 'required|date|after_or_equal:' . $entregaActual->fecha_entrega->format('Y-m-d'),
            'motivo_devolucion' => 'required|string|max:255',
            'nuevo_estado' => 'required|in:disponible,en_revision,baja',
        ], [
            'fecha_devolucion.required' => 'La fecha de devolucion es obligatoria.',
            'fecha_devolucion.after_or_equal' => 'La fecha de devolucion debe ser posterior a la fecha de entrega.',
            'motivo_devolucion.required' => 'El motivo de devolucion es obligatorio.',
            'nuevo_estado.required' => 'Debe indicar el estado del EPI tras la devolucion.',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar la entrega con fecha y motivo de devolucion
            $entregaActual->update([
                'fecha_devolucion' => $validated['fecha_devolucion'],
                'motivo_devolucion' => $validated['motivo_devolucion'],
            ]);

            // Cambiar estado del EPI
            $epiInventario->update(['estado' => $validated['nuevo_estado']]);

            DB::commit();

            return redirect()->route('epi-inventario.show', $epiInventario)
                ->with('success', 'EPI devuelto exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la devolucion: ' . $e->getMessage());
        }
    }

    /**
     * Registrar revision de EPI
     */
    public function registrarRevision(Request $request, EpiInventario $epiInventario)
    {
        $validated = $request->validate([
            'fecha_revision' => 'required|date|before_or_equal:today',
            'resultado' => 'required|in:apto,no_apto,requiere_reparacion',
            'proxima_revision' => 'nullable|date|after:fecha_revision',
            'observaciones' => 'nullable|string',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'fecha_revision.required' => 'La fecha de revision es obligatoria.',
            'resultado.required' => 'El resultado de la revision es obligatorio.',
            'proxima_revision.after' => 'La proxima revision debe ser posterior a la fecha actual.',
        ]);

        DB::beginTransaction();
        try {
            // Guardar documento si existe
            $documentoPath = null;
            if ($request->hasFile('documento')) {
                $archivo = $request->file('documento');
                $nombreArchivo = 'revision_' . $epiInventario->id . '_' . time() . '.' . $archivo->getClientOriginalExtension();
                $rutaCarpeta = 'uploads/epis/revisiones';

                if (!file_exists(public_path($rutaCarpeta))) {
                    mkdir(public_path($rutaCarpeta), 0755, true);
                }

                $archivo->move(public_path($rutaCarpeta), $nombreArchivo);
                $documentoPath = $rutaCarpeta . '/' . $nombreArchivo;
            }

            // Calcular proxima revision si no se especifica y el catalogo tiene periodicidad
            if (empty($validated['proxima_revision']) && $epiInventario->catalogo->periodicidad_revision_meses) {
                $validated['proxima_revision'] = now()->addMonths($epiInventario->catalogo->periodicidad_revision_meses);
            }

            // Crear la revision
            EpiRevision::create([
                'epi_inventario_id' => $epiInventario->id,
                'fecha_revision' => $validated['fecha_revision'],
                'resultado' => $validated['resultado'],
                'proxima_revision' => $validated['proxima_revision'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'realizado_por' => Auth::id(),
                'documento_path' => $documentoPath,
            ]);

            // Cambiar estado segun resultado
            $nuevoEstado = $epiInventario->estado;
            if ($validated['resultado'] === 'no_apto') {
                $nuevoEstado = 'baja';
            } elseif ($validated['resultado'] === 'requiere_reparacion') {
                $nuevoEstado = 'en_revision';
            } elseif ($validated['resultado'] === 'apto' && $epiInventario->estado === 'en_revision') {
                // Si estaba en revision y ahora es apto, volver a disponible (si no esta asignado)
                $entregaActual = $epiInventario->entregaActual();
                $nuevoEstado = $entregaActual ? 'asignado' : 'disponible';
            }

            if ($nuevoEstado !== $epiInventario->estado) {
                $epiInventario->update(['estado' => $nuevoEstado]);
            }

            DB::commit();

            return redirect()->route('epi-inventario.show', $epiInventario)
                ->with('success', 'Revision registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la revision: ' . $e->getMessage());
        }
    }

    /**
     * Dar de baja un EPI
     */
    public function darDeBaja(Request $request, EpiInventario $epiInventario)
    {
        // Si esta asignado, primero debe devolverse
        if ($epiInventario->estado === 'asignado') {
            return redirect()->back()
                ->with('error', 'Debe devolver el EPI antes de darlo de baja.');
        }

        $epiInventario->update(['estado' => 'baja']);

        return redirect()->route('epi-inventario.show', $epiInventario)
            ->with('success', 'EPI dado de baja exitosamente.');
    }

    /**
     * Historial global de entregas
     */
    public function historialEntregas(Request $request)
    {
        $query = EpiEntrega::with(['inventario.catalogo', 'trabajador', 'entregadoPor']);

        // Filtro por trabajador
        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        // Filtro por tipo de EPI
        if ($request->filled('epi_catalogo_id')) {
            $query->whereHas('inventario', function ($q) use ($request) {
                $q->where('epi_catalogo_id', $request->epi_catalogo_id);
            });
        }

        // Filtro por fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_entrega', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_entrega', '<=', $request->fecha_hasta);
        }

        // Filtro solo activas
        if ($request->filled('solo_activas') && $request->solo_activas === '1') {
            $query->whereNull('fecha_devolucion');
        }

        $entregas = $query->orderBy('fecha_entrega', 'desc')->paginate(50);

        // Datos para filtros
        $trabajadores = Trabajador::orderBy('apellidos')->get();
        $catalogos = EpiCatalogo::orderBy('categoria')->orderBy('nombre')->get();

        // Estadisticas
        $stats = [
            'total_entregas' => EpiEntrega::count(),
            'entregas_activas' => EpiEntrega::whereNull('fecha_devolucion')->count(),
            'entregas_mes' => EpiEntrega::whereMonth('fecha_entrega', now()->month)
                ->whereYear('fecha_entrega', now()->year)
                ->count(),
        ];

        return view('epis.entregas.index', compact('entregas', 'trabajadores', 'catalogos', 'stats'));
    }

    /**
     * Guardar firma como imagen PNG
     */
    private function guardarFirma(string $firmaBase64, int $trabajadorId): ?string
    {
        try {
            // Verificar que es una imagen base64 valida
            if (strpos($firmaBase64, 'data:image') !== 0) {
                return null;
            }

            // Extraer los datos de la imagen
            $data = explode(',', $firmaBase64);
            if (count($data) !== 2) {
                return null;
            }

            $imageData = base64_decode($data[1]);
            if ($imageData === false) {
                return null;
            }

            // Crear directorio si no existe
            $rutaCarpeta = 'uploads/epis/firmas';
            if (!file_exists(public_path($rutaCarpeta))) {
                mkdir(public_path($rutaCarpeta), 0755, true);
            }

            // Nombre del archivo
            $nombreArchivo = 'firma_' . $trabajadorId . '_' . time() . '.png';
            $rutaCompleta = public_path($rutaCarpeta . '/' . $nombreArchivo);

            // Guardar archivo
            file_put_contents($rutaCompleta, $imageData);

            return $rutaCarpeta . '/' . $nombreArchivo;
        } catch (\Exception $e) {
            return null;
        }
    }
}
