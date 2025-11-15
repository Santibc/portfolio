<?php

namespace App\Http\Controllers\ServicioTecnico;

use App\Http\Controllers\Controller;
use App\Models\STCliente;
use App\Models\STEquipo;
use App\Models\STOrdenServicio;
use App\Models\STTecnico;
use App\Models\STRepuesto;
use App\Models\STImagenOrden;
use App\Mail\OrdenServicioCreada;
use App\Mail\OrdenServicioEstadoCambiado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class STOrdenServicioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = STOrdenServicio::with(['cliente', 'equipo', 'tecnico']);

            // Filtros
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            if ($request->filled('prioridad')) {
                $query->where('prioridad', $request->prioridad);
            }
            if ($request->filled('tecnico_id')) {
                $query->where('st_tecnico_id', $request->tecnico_id);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($orden) {
                    return '
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="' . route('st.ordenes.show', $orden->id) . '" class="btn btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="' . route('st.ordenes.edit', $orden->id) . '" class="btn btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-primary" onclick="cambiarEstado(' . $orden->id . ')" title="Cambiar Estado">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    ';
                })
                ->addColumn('cliente_nombre', function ($orden) {
                    return $orden->cliente->nombre_completo_formateado;
                })
                ->addColumn('tecnico_nombre', function ($orden) {
                    return $orden->tecnico ? $orden->tecnico->nombre_completo : '<span class="text-muted">Sin asignar</span>';
                })
                ->addColumn('estado_badge', function ($orden) {
                    $badges = [
                        'recibida' => 'secondary',
                        'asignada' => 'info',
                        'en_proceso' => 'primary',
                        'pendiente_repuestos' => 'warning',
                        'completada' => 'success',
                        'entregada' => 'success',
                        'cancelada' => 'danger'
                    ];
                    $badge = $badges[$orden->estado] ?? 'secondary';
                    return '<span class="badge bg-' . $badge . '">' . ucfirst(str_replace('_', ' ', $orden->estado)) . '</span>';
                })
                ->addColumn('prioridad_badge', function ($orden) {
                    $badges = [
                        'baja' => 'secondary',
                        'media' => 'info',
                        'alta' => 'warning',
                        'urgente' => 'danger'
                    ];
                    $badge = $badges[$orden->prioridad] ?? 'secondary';
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($orden->prioridad) . '</span>';
                })
                ->addColumn('dias_transcurridos', function ($orden) {
                    $dias = $orden->dias_transcurridos;
                    $class = $dias > 7 ? 'text-danger' : ($dias > 3 ? 'text-warning' : 'text-success');
                    return '<span class="' . $class . '">' . $dias . ' días</span>';
                })
                ->filterColumn('cliente_nombre', function($query, $keyword) {
                    $query->whereHas('cliente', function($q) use ($keyword) {
                        $q->where('nombre_completo', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('tecnico_nombre', function($query, $keyword) {
                    $query->whereHas('tecnico', function($q) use ($keyword) {
                        $q->where('nombre_completo', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action', 'estado_badge', 'prioridad_badge', 'tecnico_nombre', 'dias_transcurridos'])
                ->make(true);
        }

        $tecnicos = STTecnico::activos()->get();
        return view('servicio-tecnico.ordenes.index', compact('tecnicos'));
    }

    public function create()
    {
        $clientes = STCliente::activos()->orderBy('nombre_completo')->get();
        $tecnicos = STTecnico::activos()->orderBy('nombre_completo')->get();

        // Generar número de orden
        $ultimaOrden = STOrdenServicio::latest('id')->first();
        $numeroOrden = 'ST-' . date('Y') . '-' . str_pad(($ultimaOrden ? $ultimaOrden->id + 1 : 1), 6, '0', STR_PAD_LEFT);

        return view('servicio-tecnico.ordenes.form', compact('clientes', 'tecnicos', 'numeroOrden'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_orden' => 'required|string|unique:st_ordenes_servicio,numero_orden',
            'st_cliente_id' => 'required|exists:st_clientes,id',
            'st_equipo_id' => 'nullable|exists:st_equipos,id',
            'st_tecnico_id' => 'nullable|exists:st_tecnicos,id',
            'tipo_servicio' => 'required|string',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'descripcion_problema' => 'required|string',
            'accesorios_entregados' => 'nullable|string',
            'fecha_recepcion' => 'required|date',
            'fecha_promesa_entrega' => 'nullable|date|after:fecha_recepcion',
            'observaciones' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['estado'] = 'recibida';

        if ($request->st_tecnico_id) {
            $validated['estado'] = 'asignada';
            $validated['fecha_asignacion'] = now();
        }

        $orden = STOrdenServicio::create($validated);

        // Actualizar estado del equipo si existe
        if ($orden->equipo) {
            $orden->equipo->update(['estado' => 'en_reparacion']);
        }

        // Registrar historial
        $orden->cambiarEstado($orden->estado, 'Orden de servicio creada');

        // Subir imágenes si existen
        if ($request->hasFile('imagenes')) {
            $this->subirImagenes($request, $orden);
        }

        // Enviar correo al cliente
        try {
            if ($orden->cliente && $orden->cliente->email) {
                Mail::to($orden->cliente->email)->send(new OrdenServicioCreada($orden));
            }
        } catch (\Exception $e) {
            // Log error pero no interrumpir el flujo
            \Log::error('Error al enviar correo de orden creada: ' . $e->getMessage());
        }

        return redirect()->route('st.ordenes.show', $orden->id)
            ->with('success', 'Orden de servicio creada exitosamente');
    }

    public function show(STOrdenServicio $orden)
    {
        $orden->load([
            'cliente',
            'equipo',
            'tecnico',
            'diagnosticos.tecnico',
            'repuestosUsados.repuesto',
            'historialEstados.usuario',
            'imagenes'
        ]);

        $repuestos = STRepuesto::activos()->orderBy('nombre')->get();

        return view('servicio-tecnico.ordenes.show', compact('orden', 'repuestos'));
    }

    public function edit(STOrdenServicio $orden)
    {
        $clientes = STCliente::activos()->orderBy('nombre_completo')->get();
        $tecnicos = STTecnico::activos()->orderBy('nombre_completo')->get();
        $equipos = STEquipo::where('st_cliente_id', $orden->st_cliente_id)->get();

        return view('servicio-tecnico.ordenes.form', compact('orden', 'clientes', 'tecnicos', 'equipos'));
    }

    public function update(Request $request, STOrdenServicio $orden)
    {
        $validated = $request->validate([
            'st_cliente_id' => 'required|exists:st_clientes,id',
            'st_equipo_id' => 'nullable|exists:st_equipos,id',
            'st_tecnico_id' => 'nullable|exists:st_tecnicos,id',
            'tipo_servicio' => 'required|string',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'descripcion_problema' => 'required|string',
            'accesorios_entregados' => 'nullable|string',
            'fecha_recepcion' => 'required|date',
            'fecha_promesa_entrega' => 'nullable|date|after:fecha_recepcion',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        // Detectar cambio de técnico
        if ($request->st_tecnico_id != $orden->st_tecnico_id && $request->st_tecnico_id) {
            if ($orden->estado === 'recibida') {
                $orden->cambiarEstado('asignada', 'Técnico asignado');
            }
        }

        $orden->update($validated);
        $orden->calcularCostoTotal();

        return redirect()->route('st.ordenes.show', $orden->id)
            ->with('success', 'Orden de servicio actualizada exitosamente');
    }

    public function cambiarEstado(Request $request, STOrdenServicio $orden)
    {
        $request->validate([
            'nuevo_estado' => 'required|in:recibida,asignada,en_proceso,pendiente_repuestos,completada,entregada,cancelada',
            'observaciones' => 'nullable|string'
        ]);

        // Cargar relaciones necesarias para el correo
        $orden->load(['cliente', 'equipo', 'tecnico']);

        // Guardar estado anterior para el correo
        $estadoAnterior = $orden->estado;

        $orden->cambiarEstado($request->nuevo_estado, $request->observaciones);

        // Si se completa o entrega, actualizar estado del equipo
        if (in_array($request->nuevo_estado, ['completada', 'entregada']) && $orden->equipo) {
            $orden->equipo->update(['estado' => 'operativo']);
        }

        // Enviar correo al cliente notificando el cambio de estado
        try {
            if ($orden->cliente && $orden->cliente->email) {
                Mail::to($orden->cliente->email)->send(
                    new OrdenServicioEstadoCambiado(
                        $orden,
                        $estadoAnterior,
                        $request->nuevo_estado,
                        $request->observaciones
                    )
                );
            }
        } catch (\Exception $e) {
            // Log error pero no interrumpir el flujo
            \Log::error('Error al enviar correo de cambio de estado: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente'
        ]);
    }

    public function getEquiposByCliente($clienteId)
    {
        $equipos = STEquipo::where('st_cliente_id', $clienteId)
            ->activos()
            ->get();

        return response()->json($equipos);
    }

    private function subirImagenes(Request $request, STOrdenServicio $orden)
    {
        $orden_dir = 'imagenes/servicio-tecnico/ordenes/' . $orden->id;

        if (!file_exists(public_path($orden_dir))) {
            mkdir(public_path($orden_dir), 0777, true);
        }

        foreach ($request->file('imagenes') as $index => $imagen) {
            $filename = time() . '_' . $index . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path($orden_dir), $filename);

            STImagenOrden::create([
                'st_orden_servicio_id' => $orden->id,
                'nombre_archivo' => $filename,
                'ruta_archivo' => $orden_dir . '/' . $filename,
                'tipo_imagen' => $request->tipos_imagen[$index] ?? 'recepcion',
                'orden' => $index
            ]);
        }
    }

    public function agregarDiagnostico(Request $request, STOrdenServicio $orden)
    {
        $validated = $request->validate([
            'st_tecnico_id' => 'required|exists:st_tecnicos,id',
            'diagnostico_tecnico' => 'required|string',
            'reparaciones_realizadas' => 'nullable|string',
            'recomendaciones' => 'nullable|string',
            'requiere_repuestos' => 'boolean',
            'repuestos_necesarios' => 'nullable|string',
            'tiempo_estimado_horas' => 'nullable|numeric|min:0',
            'costo_estimado' => 'nullable|numeric|min:0'
        ]);

        $validated['st_orden_servicio_id'] = $orden->id;
        $validated['fecha_diagnostico'] = now()->toDateString();
        $validated['requiere_repuestos'] = $request->has('requiere_repuestos');
        $validated['fallas_encontradas'] = $validated['diagnostico_tecnico']; // Copiar el diagnóstico a fallas encontradas

        \App\Models\STDiagnostico::create($validated);

        return redirect()->route('st.ordenes.show', $orden)
            ->with('success', 'Diagnóstico agregado exitosamente');
    }

    public function agregarRepuesto(Request $request, STOrdenServicio $orden)
    {
        $validated = $request->validate([
            'st_repuesto_id' => 'required|exists:st_repuestos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0'
        ]);

        $repuesto = \App\Models\STRepuesto::findOrFail($validated['st_repuesto_id']);

        // Verificar stock
        if ($repuesto->stock_actual < $validated['cantidad']) {
            return redirect()->route('st.ordenes.show', $orden)
                ->with('error', 'Stock insuficiente. Disponible: ' . $repuesto->stock_actual);
        }

        // Crear registro de repuesto usado
        \App\Models\STRepuestoUsado::create([
            'st_orden_servicio_id' => $orden->id,
            'st_repuesto_id' => $validated['st_repuesto_id'],
            'cantidad' => $validated['cantidad'],
            'precio_unitario' => $validated['precio_unitario']
        ]);

        // Descontar del stock
        $repuesto->decrement('stock_actual', $validated['cantidad']);

        // Recalcular costo total de la orden
        $orden->calcularCostoTotal();

        return redirect()->route('st.ordenes.show', $orden)
            ->with('success', 'Repuesto agregado exitosamente');
    }
}
