<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Models\Cliente;
use App\Models\Subcontrata;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContratoController extends Controller
{
    /**
     * Mostrar listado de contratos con filtros y estadísticas.
     */
    public function index(Request $request): View
    {
        $query = Contrato::with(['tipo', 'cliente', 'subcontrata']);

        // Filtros
        if ($request->filled('search')) {
            $query->buscar($request->search);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('contrato_tipo_id')) {
            $query->where('contrato_tipo_id', $request->contrato_tipo_id);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('subcontrata_id')) {
            $query->where('subcontrata_id', $request->subcontrata_id);
        }
        if ($request->filled('tiene_retencion')) {
            $query->where('tiene_retencion', $request->tiene_retencion === '1');
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_fin', '<=', $request->fecha_hasta);
        }

        // Ordenar y paginar
        $contratos = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        // Estadísticas
        $stats = [
            'total' => Contrato::count(),
            'activos' => Contrato::activos()->count(),
            'proximos_vencer' => Contrato::proximosAVencer(30)->count(),
            'importe_retenido' => Contrato::garantiasPendientes()->sum('importe_retenido'),
        ];

        // Datos para filtros
        $tipos = ContratoTipo::orderBy('nombre')->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $subcontratas = Subcontrata::where('activa', true)->orderBy('nombre')->get();

        return view('contratos.index', compact(
            'contratos', 'stats', 'tipos', 'clientes', 'subcontratas'
        ));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create(): View
    {
        $tipos = ContratoTipo::orderBy('nombre')->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $subcontratas = Subcontrata::where('activa', true)->orderBy('nombre')->get();
        $codigoSugerido = Contrato::generarCodigo();

        return view('contratos.create', compact(
            'tipos', 'clientes', 'subcontratas', 'codigoSugerido'
        ));
    }

    /**
     * Guardar nuevo contrato.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_tipo_id' => 'required|exists:contrato_tipos,id',
            'codigo' => 'nullable|string|max:50|unique:contratos,codigo',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cliente_id' => 'nullable|exists:clientes,id',
            'subcontrata_id' => 'nullable|exists:subcontratas,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'fecha_firma' => 'nullable|date',
            'importe' => 'nullable|numeric|min:0',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'tiene_retencion' => 'boolean',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_liberacion_garantia' => 'nullable|date',
            'documento' => 'nullable|file|max:10240',
            'notas' => 'nullable|string',
            'renovacion_automatica' => 'boolean',
            'dias_preaviso_vencimiento' => 'nullable|integer|min:1|max:365',
        ], [
            'contrato_tipo_id.required' => 'El tipo de contrato es obligatorio.',
            'titulo.required' => 'El título es obligatorio.',
            'codigo.unique' => 'Este código ya está en uso.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
            'documento.max' => 'El documento no puede superar 10MB.',
        ]);

        DB::beginTransaction();
        try {
            // Generar código si no se proporcionó
            if (empty($validated['codigo'])) {
                $validated['codigo'] = Contrato::generarCodigo();
            }

            // Preparar datos
            $data = [
                'contrato_tipo_id' => $validated['contrato_tipo_id'],
                'codigo' => $validated['codigo'],
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'] ?? null,
                'cliente_id' => $validated['cliente_id'] ?? null,
                'subcontrata_id' => $validated['subcontrata_id'] ?? null,
                'fecha_inicio' => $validated['fecha_inicio'] ?? null,
                'fecha_fin' => $validated['fecha_fin'] ?? null,
                'fecha_firma' => $validated['fecha_firma'] ?? null,
                'importe' => $validated['importe'] ?? null,
                'iva_porcentaje' => $validated['iva_porcentaje'],
                'tiene_retencion' => $validated['tiene_retencion'] ?? false,
                'retencion_porcentaje' => null,
                'importe_retenido' => null,
                'fecha_liberacion_garantia' => $validated['fecha_liberacion_garantia'] ?? null,
                'estado' => Contrato::ESTADO_BORRADOR,
                'notas' => $validated['notas'] ?? null,
                'renovacion_automatica' => $validated['renovacion_automatica'] ?? false,
                'dias_preaviso_vencimiento' => $validated['dias_preaviso_vencimiento'] ?? 30,
            ];

            // Calcular importe retenido si aplica
            if (!empty($data['tiene_retencion']) && !empty($validated['retencion_porcentaje'])) {
                $importe = floatval($data['importe'] ?? 0);
                $data['retencion_porcentaje'] = $validated['retencion_porcentaje'];
                $data['importe_retenido'] = $importe * (floatval($validated['retencion_porcentaje']) / 100);
                $data['estado_garantia'] = Contrato::ESTADO_GARANTIA_PENDIENTE;
            }

            $contrato = Contrato::create($data);

            // Subir documento si existe
            if ($request->hasFile('documento')) {
                $documento = $request->file('documento');
                $rutaCarpeta = 'uploads/contratos/' . $contrato->id;

                if (!file_exists(public_path($rutaCarpeta))) {
                    mkdir(public_path($rutaCarpeta), 0755, true);
                }

                $nombreArchivo = 'contrato_' . time() . '.' . $documento->getClientOriginalExtension();
                $documento->move(public_path($rutaCarpeta), $nombreArchivo);

                $contrato->update(['documento_path' => $rutaCarpeta . '/' . $nombreArchivo]);
            }

            // Registrar en auditoría
            Auditoria::registrar('crear', 'contratos', $contrato->id, null, $contrato->toArray());

            DB::commit();

            return redirect()->route('contratos.show', $contrato)
                ->with('success', 'Contrato creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear el contrato: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle del contrato.
     */
    public function show(Contrato $contrato): View
    {
        $contrato->load(['tipo', 'cliente', 'subcontrata', 'responsable', 'obras']);

        return view('contratos.show', compact('contrato'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Contrato $contrato): View
    {
        $tipos = ContratoTipo::orderBy('nombre')->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $subcontratas = Subcontrata::where('activa', true)->orderBy('nombre')->get();

        return view('contratos.edit', compact('contrato', 'tipos', 'clientes', 'subcontratas'));
    }

    /**
     * Actualizar contrato existente.
     */
    public function update(Request $request, Contrato $contrato)
    {
        $validated = $request->validate([
            'contrato_tipo_id' => 'required|exists:contrato_tipos,id',
            'codigo' => 'nullable|string|max:50|unique:contratos,codigo,' . $contrato->id,
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cliente_id' => 'nullable|exists:clientes,id',
            'subcontrata_id' => 'nullable|exists:subcontratas,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'fecha_firma' => 'nullable|date',
            'importe' => 'nullable|numeric|min:0',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'tiene_retencion' => 'boolean',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_liberacion_garantia' => 'nullable|date',
            'documento' => 'nullable|file|max:10240',
            'notas' => 'nullable|string',
            'renovacion_automatica' => 'boolean',
            'dias_preaviso_vencimiento' => 'nullable|integer|min:1|max:365',
        ], [
            'contrato_tipo_id.required' => 'El tipo de contrato es obligatorio.',
            'titulo.required' => 'El título es obligatorio.',
            'codigo.unique' => 'Este código ya está en uso.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
            'documento.max' => 'El documento no puede superar 10MB.',
        ]);

        DB::beginTransaction();
        try {
            // Preparar datos
            $data = [
                'contrato_tipo_id' => $validated['contrato_tipo_id'],
                'codigo' => $validated['codigo'] ?? $contrato->codigo,
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'] ?? null,
                'cliente_id' => $validated['cliente_id'] ?? null,
                'subcontrata_id' => $validated['subcontrata_id'] ?? null,
                'fecha_inicio' => $validated['fecha_inicio'] ?? null,
                'fecha_fin' => $validated['fecha_fin'] ?? null,
                'fecha_firma' => $validated['fecha_firma'] ?? null,
                'importe' => $validated['importe'] ?? null,
                'iva_porcentaje' => $validated['iva_porcentaje'],
                'tiene_retencion' => $validated['tiene_retencion'] ?? false,
                'fecha_liberacion_garantia' => $validated['fecha_liberacion_garantia'] ?? null,
                'notas' => $validated['notas'] ?? null,
                'renovacion_automatica' => $validated['renovacion_automatica'] ?? false,
                'dias_preaviso_vencimiento' => $validated['dias_preaviso_vencimiento'] ?? 30,
            ];

            // Recalcular importe retenido si aplica
            if (!empty($data['tiene_retencion']) && !empty($validated['retencion_porcentaje'])) {
                $importe = floatval($data['importe'] ?? 0);
                $data['retencion_porcentaje'] = $validated['retencion_porcentaje'];
                $data['importe_retenido'] = $importe * (floatval($validated['retencion_porcentaje']) / 100);
            } else {
                $data['retencion_porcentaje'] = null;
                $data['importe_retenido'] = null;
                $data['tiene_retencion'] = false;
            }

            // Subir nuevo documento si existe
            if ($request->hasFile('documento')) {
                // Eliminar documento anterior si existe
                if ($contrato->documento_path && file_exists(public_path($contrato->documento_path))) {
                    unlink(public_path($contrato->documento_path));
                }

                $documento = $request->file('documento');
                $rutaCarpeta = 'uploads/contratos/' . $contrato->id;

                if (!file_exists(public_path($rutaCarpeta))) {
                    mkdir(public_path($rutaCarpeta), 0755, true);
                }

                $nombreArchivo = 'contrato_' . time() . '.' . $documento->getClientOriginalExtension();
                $documento->move(public_path($rutaCarpeta), $nombreArchivo);

                $data['documento_path'] = $rutaCarpeta . '/' . $nombreArchivo;
            }

            // Guardar datos anteriores para auditoría
            $datosAnteriores = $contrato->toArray();

            $contrato->update($data);

            // Registrar en auditoría
            Auditoria::registrar('editar', 'contratos', $contrato->id, $datosAnteriores, $contrato->fresh()->toArray());

            DB::commit();

            return redirect()->route('contratos.show', $contrato)
                ->with('success', 'Contrato actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el contrato: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar contrato (soft delete).
     */
    public function destroy(Contrato $contrato): JsonResponse
    {
        try {
            // Registrar en auditoría antes de eliminar
            Auditoria::registrar('eliminar', 'contratos', $contrato->id, $contrato->toArray(), null);

            // Eliminar documento físico si existe
            if ($contrato->documento_path && file_exists(public_path($contrato->documento_path))) {
                unlink(public_path($contrato->documento_path));
            }

            // Eliminar carpeta si queda vacía
            $carpeta = public_path('uploads/contratos/' . $contrato->id);
            if (is_dir($carpeta)) {
                $archivos = array_diff(scandir($carpeta), ['.', '..']);
                if (count($archivos) === 0) {
                    rmdir($carpeta);
                }
            }

            $contrato->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contrato eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el contrato: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // CAMBIO DE ESTADO
    // ==========================================

    /**
     * Activar contrato (de borrador a activo).
     */
    public function activar(Contrato $contrato): JsonResponse
    {
        try {
            if (!$contrato->activar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden activar contratos en estado borrador.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contrato activado correctamente.',
                'estado' => $contrato->estado,
                'estado_label' => Contrato::ESTADOS[$contrato->estado],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancelar contrato.
     */
    public function cancelar(Contrato $contrato): JsonResponse
    {
        try {
            if (!$contrato->cancelar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar un contrato vencido o ya cancelado.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contrato cancelado.',
                'estado' => $contrato->estado,
                'estado_label' => Contrato::ESTADOS[$contrato->estado],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar contrato como vencido.
     */
    public function marcarVencido(Contrato $contrato): JsonResponse
    {
        try {
            if (!$contrato->marcarVencido()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden marcar como vencidos los contratos activos.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contrato marcado como vencido.',
                'estado' => $contrato->estado,
                'estado_label' => Contrato::ESTADOS[$contrato->estado],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reactivar contrato vencido.
     */
    public function reactivar(Contrato $contrato): JsonResponse
    {
        try {
            if (!$contrato->reactivar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden reactivar contratos vencidos.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contrato reactivado correctamente.',
                'estado' => $contrato->estado,
                'estado_label' => Contrato::ESTADOS[$contrato->estado],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // GESTIÓN DE GARANTÍAS
    // ==========================================

    /**
     * Liberar garantía retenida (soporta liberación parcial).
     */
    public function liberarGarantia(Request $request, Contrato $contrato): JsonResponse
    {
        $validated = $request->validate([
            'fecha_liberacion' => 'required|date',
            'porcentaje' => 'required|integer|min:1|max:100',
            'notas' => 'nullable|string|max:500',
        ], [
            'fecha_liberacion.required' => 'La fecha de liberación es obligatoria.',
            'porcentaje.required' => 'Debe especificar el porcentaje a liberar.',
            'porcentaje.integer' => 'El porcentaje debe ser un número entero.',
            'porcentaje.min' => 'El porcentaje mínimo es 1%.',
            'porcentaje.max' => 'El porcentaje máximo es 100%.',
        ]);

        try {
            if (!$contrato->tiene_retencion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este contrato no tiene retención de garantía.',
                ], 422);
            }

            // Validar que no esté 100% liberado
            if (intval($contrato->porcentaje_total_liberado ?? 0) >= 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'La garantía ya fue liberada completamente.',
                ], 422);
            }

            // Validar porcentaje disponible
            $porcentajeDisponible = 100 - intval($contrato->porcentaje_total_liberado ?? 0);
            if ($validated['porcentaje'] > $porcentajeDisponible) {
                return response()->json([
                    'success' => false,
                    'message' => "Solo puede liberar hasta {$porcentajeDisponible}% restante.",
                ], 422);
            }

            \DB::beginTransaction();

            $contrato->liberarGaranciaParcial(
                $validated['porcentaje'],
                $validated['fecha_liberacion'],
                $validated['notas'] ?? null
            );

            \DB::commit();

            $contratoActualizado = $contrato->fresh();
            $mensaje = $validated['porcentaje'] >= $porcentajeDisponible
                ? 'Garantía liberada completamente.'
                : "Liberado {$validated['porcentaje']}% de la garantía.";

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'importe_liberado' => number_format(
                    floatval($contrato->importe_retenido) * ($validated['porcentaje'] / 100),
                    2, ',', '.'
                ),
                'porcentaje_total' => number_format($contratoActualizado->porcentaje_total_liberado, 0),
                'porcentaje_pendiente' => number_format($contratoActualizado->porcentaje_pendiente_liberar, 0),
                'estado_garantia' => $contratoActualizado->estado_garantia,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener historial de liberaciones de garantía.
     */
    public function historialLiberaciones(Contrato $contrato): JsonResponse
    {
        try {
            $liberaciones = $contrato->liberaciones()
                ->with('usuario:id,name')
                ->ordenCronologico()
                ->get()
                ->map(function ($lib) {
                    return [
                        'id' => $lib->id,
                        'fecha' => $lib->fecha_liberacion->format('d/m/Y'),
                        'porcentaje' => $lib->porcentaje_liberado . '%',
                        'importe' => number_format($lib->importe_liberado, 2, ',', '.') . ' €',
                        'notas' => $lib->notas,
                        'usuario' => $lib->usuario->name ?? 'Sistema',
                        'fecha_registro' => $lib->created_at->format('d/m/Y H:i'),
                    ];
                });

            return response()->json([
                'success' => true,
                'liberaciones' => $liberaciones,
                'resumen' => [
                    'total_liberado' => $contrato->porcentaje_total_liberado . '%',
                    'importe_liberado' => number_format($contrato->importe_total_liberado, 2, ',', '.') . ' €',
                    'pendiente' => $contrato->porcentaje_pendiente_liberar . '%',
                    'importe_pendiente' => number_format($contrato->importe_pendiente_liberar, 2, ',', '.') . ' €',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
