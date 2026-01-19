<?php

namespace App\Http\Controllers;

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
        if (!$user->hasAnyRole(['admin', 'facturacion'])) {
            abort(403, 'No tiene permisos para registrar pagos');
        }

        // Verificar que la cotización pueda recibir pagos
        if (!$solicitud->puedeRegistrarPago()) {
            return redirect()->route('solicitudes.index')
                ->with('error', 'Esta cotización no puede recibir pagos en su estado actual');
        }

        $solicitud->load(['cliente', 'items', 'cliente.vendedor']);

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
        if (!$user->hasAnyRole(['admin', 'facturacion'])) {
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

        // Validar datos
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $solicitud->saldo_pendiente,
            'metodo_pago' => 'required|in:' . implode(',', array_keys(SolicitudCotizacion::METODOS_PAGO)),
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notas_pago' => 'nullable|string|max:1000',
        ], [
            'monto.required' => 'El monto es obligatorio',
            'monto.min' => 'El monto debe ser mayor a 0',
            'monto.max' => 'El monto no puede superar el saldo pendiente',
            'metodo_pago.required' => 'Seleccione un método de pago',
            'metodo_pago.in' => 'Método de pago no válido',
            'comprobante.mimes' => 'El comprobante debe ser PDF, JPG o PNG',
            'comprobante.max' => 'El comprobante no puede superar 5MB',
        ]);

        try {
            // Procesar archivo de comprobante
            $rutaComprobante = null;
            if ($request->hasFile('comprobante')) {
                $archivo = $request->file('comprobante');
                $nombreArchivo = 'pago_' . $solicitud->numero_solicitud . '_' . time() . '.' . $archivo->getClientOriginalExtension();
                $rutaComprobante = $archivo->storeAs('comprobantes_pago', $nombreArchivo, 'public');
            }

            // Registrar pago
            $solicitud->registrarPago(
                $validated['monto'],
                $validated['metodo_pago'],
                $rutaComprobante,
                $validated['notas_pago'] ?? null,
                $user->id
            );

            Log::info("Pago registrado para cotización {$solicitud->numero_solicitud} por usuario {$user->id}");

            return response()->json([
                'success' => true,
                'mensaje' => $solicitud->estaPagada()
                    ? 'Pago registrado exitosamente. La cotización está completamente pagada.'
                    : 'Pago parcial registrado exitosamente. Saldo pendiente: $' . number_format($solicitud->fresh()->saldo_pendiente, 0),
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
     * Ver detalle de pago
     */
    public function show(SolicitudCotizacion $solicitud)
    {
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'facturacion', 'vendedor'])) {
            abort(403);
        }

        $solicitud->load(['cliente', 'verificadoPor']);

        return response()->json([
            'success' => true,
            'pago' => [
                'estado' => $solicitud->estado_pago,
                'etiqueta' => $solicitud->etiqueta_estado_pago,
                'monto_total' => $solicitud->monto_total,
                'monto_pagado' => $solicitud->monto_pagado,
                'saldo_pendiente' => $solicitud->saldo_pendiente,
                'metodo_pago' => $solicitud->metodo_pago ? SolicitudCotizacion::METODOS_PAGO[$solicitud->metodo_pago] : null,
                'comprobante' => $solicitud->comprobante_pago ? asset('storage/' . $solicitud->comprobante_pago) : null,
                'pagado_en' => $solicitud->pagado_en?->format('d/m/Y H:i'),
                'verificado_por' => $solicitud->verificadoPor?->name,
                'verificado_en' => $solicitud->verificado_en?->format('d/m/Y H:i'),
                'notas' => $solicitud->notas_pago,
            ],
        ]);
    }

    /**
     * Descargar comprobante de pago
     */
    public function descargarComprobante(SolicitudCotizacion $solicitud)
    {
        if (!$solicitud->comprobante_pago) {
            abort(404, 'No hay comprobante de pago');
        }

        $path = storage_path('app/public/' . $solicitud->comprobante_pago);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($path);
    }
}
