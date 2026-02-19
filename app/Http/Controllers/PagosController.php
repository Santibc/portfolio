<?php

namespace App\Http\Controllers;

use App\Models\PagoSolicitud;
use App\Models\SolicitudCotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador para gestión de pagos de cotizaciones
 */
class PagosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar formulario de confirmación de pago
     */
    public function create(SolicitudCotizacion $solicitud)
    {
        // Verificar permisos
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'auxiliar_administrativo', 'facturacion', 'vendedor', 'inventarios'])) {
            abort(403, 'No tiene permisos para registrar pagos');
        }

        // Verificar que la cotización pueda recibir pagos
        if (!$solicitud->puedeRegistrarPago()) {
            return redirect()->route('solicitudes.index')
                ->with('error', 'Esta cotización no puede recibir pagos en su estado actual');
        }

        $solicitud->load(['cliente', 'items', 'cliente.vendedor', 'pagos.registradoPor', 'pagos.aprobadoPor']);

        return view('solicitudes.confirmar-pago', [
            'solicitud' => $solicitud,
            'metodosPago' => SolicitudCotizacion::METODOS_PAGO,
        ]);
    }

    /**
     * Registrar pago de cotización
     */
    public function store(Request $request, SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$user->hasAnyRole(['admin', 'auxiliar_administrativo', 'facturacion', 'vendedor', 'inventarios'])) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para registrar pagos'
            ], 403);
        }

        // Verificar que pueda recibir pagos
        if (!$solicitud->puedeRegistrarPago()) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Esta cotización no puede recibir pagos'
            ], 400);
        }

        // Calcular máximo permitido descontando pagos pendientes de aprobación
        $montoPendienteAprobacion = $solicitud->pagosPendientes()->sum('monto');
        $maxPermitido = $solicitud->saldo_pendiente - $montoPendienteAprobacion;

        if ($maxPermitido <= 0) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Ya existen pagos pendientes de aprobación que cubren el saldo restante'
            ], 400);
        }

        // Validar datos
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $maxPermitido,
            'metodo_pago' => 'required|in:' . implode(',', array_keys(SolicitudCotizacion::METODOS_PAGO)),
            'comprobantes' => 'nullable|array|max:10',
            'comprobantes.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notas_pago' => 'nullable|string|max:1000',
            'forma_pago' => 'nullable|string|max:50',
            'dias_vencimiento' => 'nullable|integer|min:0|max:365',
        ], [
            'monto.required' => 'El monto es obligatorio',
            'monto.min' => 'El monto debe ser mayor a 0',
            'monto.max' => 'El monto no puede superar el saldo disponible',
            'metodo_pago.required' => 'Seleccione un método de pago',
            'metodo_pago.in' => 'Método de pago no válido',
            'comprobantes.*.mimes' => 'Los comprobantes deben ser PDF, JPG o PNG',
            'comprobantes.*.max' => 'Cada comprobante no puede superar 5MB',
        ]);

        try {
            // Procesar archivos de comprobante (múltiples)
            $rutasComprobantes = null;
            if ($request->hasFile('comprobantes')) {
                $rutasComprobantes = [];
                foreach ($request->file('comprobantes') as $index => $archivo) {
                    $nombreArchivo = 'pago_' . $solicitud->numero_solicitud . '_' . time() . '_' . ($index + 1) . '.' . $archivo->getClientOriginalExtension();
                    $rutasComprobantes[] = $archivo->storeAs('comprobantes_pago', $nombreArchivo, 'public');
                }
            }

            // Guardar forma de pago si se proporcionó y no existe
            if (!$solicitud->forma_pago_factura && !empty($validated['forma_pago'])) {
                $formaPago = $validated['forma_pago'];
                $diasVencimiento = $validated['dias_vencimiento'] ?? 0;
                $solicitud->forma_pago_factura = $formaPago;
                if ($diasVencimiento > 0) {
                    $solicitud->fecha_vencimiento = now()->addDays($diasVencimiento);
                }
                $solicitud->save();
            }

            // Registrar pago (queda pendiente de aprobación)
            $solicitud->registrarPago(
                $validated['monto'],
                $validated['metodo_pago'],
                $rutasComprobantes,
                $validated['notas_pago'] ?? null,
                $user->id
            );

            // Si es crédito, auto-aprobar el pago
            $mensaje = 'Pago registrado exitosamente. Está pendiente de aprobación por el área de facturación.';
            if (
                ($solicitud->forma_pago_factura && str_contains($solicitud->forma_pago_factura, 'Crédito'))
                || $validated['metodo_pago'] === 'credito'
            ) {
                $ultimoPago = $solicitud->pagos()->latest()->first();
                if ($ultimoPago && $ultimoPago->estaPendiente()) {
                    $ultimoPago->update([
                        'estado' => PagoSolicitud::ESTADO_APROBADO,
                        'aprobado_por' => $user->id,
                        'aprobado_en' => now(),
                    ]);
                    $solicitud->recalcularPagos();
                    $mensaje = 'Pago a crédito registrado y aprobado automáticamente.';
                }
            }

            Log::info("Pago registrado para cotización {$solicitud->numero_solicitud} por usuario {$user->id}");

            return response()->json([
                'success' => true,
                'mensaje' => $mensaje,
                'estado_pago' => $solicitud->fresh()->estado_pago,
            ]);

        } catch (\Exception $e) {
            Log::error("Error al registrar pago: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'mensaje' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aprobar un pago pendiente
     */
    public function aprobar(SolicitudCotizacion $solicitud, PagoSolicitud $pago)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'auxiliar_administrativo', 'facturacion'])) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para aprobar pagos'
            ], 403);
        }

        if ($pago->solicitud_cotizacion_id !== $solicitud->id) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El pago no pertenece a esta cotización'
            ], 404);
        }

        if (!$pago->estaPendiente()) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Este pago ya fue procesado'
            ], 400);
        }

        try {
            $solicitud->aprobarPago($pago, $user->id);

            Log::info("Pago #{$pago->id} aprobado para cotización {$solicitud->numero_solicitud} por usuario {$user->id}");

            return response()->json([
                'success' => true,
                'mensaje' => 'Pago aprobado exitosamente',
                'estado_pago' => $solicitud->fresh()->estado_pago,
            ]);

        } catch (\Exception $e) {
            Log::error("Error al aprobar pago: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al aprobar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar un pago pendiente
     */
    public function rechazar(SolicitudCotizacion $solicitud, PagoSolicitud $pago)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'auxiliar_administrativo', 'facturacion'])) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No tiene permisos para rechazar pagos'
            ], 403);
        }

        if ($pago->solicitud_cotizacion_id !== $solicitud->id) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El pago no pertenece a esta cotización'
            ], 404);
        }

        if (!$pago->estaPendiente()) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Este pago ya fue procesado'
            ], 400);
        }

        try {
            $solicitud->rechazarPago($pago, $user->id);

            Log::info("Pago #{$pago->id} rechazado para cotización {$solicitud->numero_solicitud} por usuario {$user->id}");

            return response()->json([
                'success' => true,
                'mensaje' => 'Pago rechazado',
            ]);

        } catch (\Exception $e) {
            Log::error("Error al rechazar pago: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al rechazar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver detalle de pago
     */
    public function show(SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'auxiliar_administrativo', 'facturacion', 'vendedor', 'inventarios'])) {
            abort(403);
        }

        $solicitud->load(['cliente', 'verificadoPor', 'pagos.registradoPor', 'pagos.aprobadoPor']);

        $metodosPago = SolicitudCotizacion::METODOS_PAGO;

        return response()->json([
            'success' => true,
            'pago' => [
                'estado' => $solicitud->estado_pago,
                'etiqueta' => $solicitud->etiqueta_estado_pago,
                'monto_total' => $solicitud->monto_total,
                'monto_pagado' => $solicitud->monto_pagado,
                'saldo_pendiente' => $solicitud->saldo_pendiente,
                'metodo_pago' => $solicitud->metodo_pago ? $metodosPago[$solicitud->metodo_pago] : null,
                'comprobante' => $solicitud->comprobante_pago ? url('/solicitudes/' . $solicitud->id . '/comprobante') : null,
                'pagado_en' => $solicitud->pagado_en?->format('d/m/Y H:i'),
                'verificado_por' => $solicitud->verificadoPor?->name,
                'verificado_en' => $solicitud->verificado_en?->format('d/m/Y H:i'),
                'notas' => $solicitud->notas_pago,
            ],
            'historial' => $solicitud->pagos->sortBy('created_at')->values()->map(function ($pago) use ($metodosPago, $solicitud) {
                return [
                    'id' => $pago->id,
                    'fecha' => $pago->created_at->format('d/m/Y H:i'),
                    'monto' => $pago->monto,
                    'metodo_pago' => $metodosPago[$pago->metodo_pago] ?? $pago->metodo_pago,
                    'registrado_por' => $pago->registradoPor?->name,
                    'notas' => $pago->notas,
                    'estado' => $pago->estado,
                    'etiqueta_estado' => $pago->etiqueta_estado,
                    'color_estado' => $pago->color_estado,
                    'aprobado_por' => $pago->aprobadoPor?->name,
                    'aprobado_en' => $pago->aprobado_en?->format('d/m/Y H:i'),
                    'comprobantes_urls' => $pago->comprobante
                        ? collect(is_string($pago->comprobante) ? [$pago->comprobante] : $pago->comprobante)
                            ->map(fn($c, $i) => url('/solicitudes/' . $solicitud->id . '/pagos/' . $pago->id . '/comprobante?index=' . $i))
                            ->values()->toArray()
                        : [],
                ];
            }),
        ]);
    }

    /**
     * Descargar comprobante de pago (legacy - último comprobante)
     */
    public function descargarComprobante(SolicitudCotizacion $solicitud)
    {
        if (!$solicitud->comprobante_pago) {
            abort(404, 'No hay comprobante de pago');
        }

        $path = Storage::disk('public')->path($solicitud->comprobante_pago);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($path);
    }

    /**
     * Descargar comprobante de un pago individual
     */
    public function descargarComprobantePago(Request $request, SolicitudCotizacion $solicitud, PagoSolicitud $pago)
    {
        if ($pago->solicitud_cotizacion_id !== $solicitud->id) {
            abort(404, 'Pago no encontrado');
        }

        if (!$pago->comprobante) {
            abort(404, 'No hay comprobante para este pago');
        }

        $comprobantes = $pago->comprobante;
        $index = (int) $request->query('index', 0);

        // Compatibilidad: si es string (dato antiguo), convertir a array
        if (is_string($comprobantes)) {
            $comprobantes = [$comprobantes];
        }

        if (!isset($comprobantes[$index])) {
            abort(404, 'Archivo no encontrado');
        }

        $path = Storage::disk('public')->path($comprobantes[$index]);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($path);
    }
}
