<?php

namespace App\Http\Controllers;

use App\Models\TransaccionPago;
use App\Models\ConfiguracionPasarela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Manejar webhook de Wompi
     */
    public function wompi(Request $request)
    {
        // Log para debugging
        Log::info('Webhook Wompi recibido', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        try {
            // Verificar firma
            $config = ConfiguracionPasarela::obtenerConfiguracionActiva('wompi');
            if (!$config || !$config->event_key) {
                Log::error('Configuración de Wompi no encontrada');
                return response()->json(['error' => 'Configuración no válida'], 500);
            }

            $firmaRecibida = $request->header('X-Event-Signature');
            if (!$firmaRecibida) {
                Log::warning('Webhook sin firma');
                return response()->json(['error' => 'Sin firma'], 401);
            }

            // Verificar firma
            $payload = $request->getContent();
            $firmaEsperada = hash_hmac('sha256', $payload, $config->event_key);
            
            if (!hash_equals($firmaEsperada, $firmaRecibida)) {
                Log::warning('Firma inválida del webhook');
                return response()->json(['error' => 'Firma inválida'], 401);
            }

            // Procesar evento
            $data = json_decode($payload, true);
            $event = $data['event'] ?? null;
            $eventData = $data['data'] ?? [];

            // Obtener referencia según el tipo de evento
            $reference = null;
            $transactionId = null;
            $status = null;

            // Wompi puede enviar diferentes estructuras según el evento
            if (isset($eventData['transaction'])) {
                // Estructura con transaction anidada
                $reference = $eventData['transaction']['reference'] ?? null;
                $transactionId = $eventData['transaction']['id'] ?? null;
                $status = $eventData['transaction']['status'] ?? null;
            } else {
                // Estructura plana
                $reference = $eventData['reference'] ?? null;
                $transactionId = $eventData['id'] ?? null;
                $status = $eventData['status'] ?? null;
            }

            if (!$reference) {
                Log::error('Referencia no encontrada en webhook', $data);
                return response()->json(['error' => 'Sin referencia'], 400);
            }

            // Buscar transacción local
            $transaccion = TransaccionPago::where('referencia_transaccion', $reference)->first();
            
            if (!$transaccion) {
                Log::warning("Transacción no encontrada: {$reference}");
                return response()->json(['error' => 'Transacción no encontrada'], 404);
            }

            // Registrar evento
            $transaccion->registrarEvento($event, $eventData, $request->ip());

            // Procesar según estado
            $this->procesarEstadoTransaccion($transaccion, $status, $transactionId, $eventData);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    /**
     * Procesar estado de transacción
     */
    private function procesarEstadoTransaccion($transaccion, $status, $transactionId, $data)
    {
        // Evitar reprocesar
        if (in_array($transaccion->estado, ['aprobada', 'rechazada'])) {
            Log::info("Transacción ya procesada: {$transaccion->referencia_transaccion}");
            return;
        }

        // VERIFICAR SI ES PAGO DE MEMBRESÍA
        $esPagoMembresia = strpos($transaccion->referencia_transaccion, 'MEM-') === 0;
        
        if ($esPagoMembresia) {
            $this->procesarPagoMembresia($transaccion, $status, $transactionId, $data);
            return;
        }

        // PROCESAR COMPRA NORMAL
        switch ($status) {
            case 'APPROVED':
                $transaccion->update([
                    'estado' => 'aprobada',
                    'id_transaccion_pasarela' => $transactionId,
                    'metodo_pago' => $data['payment_method_type'] ?? $data['transaction']['payment_method_type'] ?? null,
                    'fecha_procesamiento' => now(),
                    'respuesta_pasarela' => $data,
                    'codigo_autorizacion' => $data['authorization_code'] ?? $data['transaction']['authorization_code'] ?? null
                ]);

                // Actualizar compra
                $compra = $transaccion->compra;
                $compra->update(['estado' => 'pagada']);

                // Generar comisión
                $compra->generarComision();

                Log::info("Pago aprobado: {$transaccion->referencia_transaccion}");
                break;

            case 'DECLINED':
            case 'VOIDED':
                $transaccion->update([
                    'estado' => 'rechazada',
                    'id_transaccion_pasarela' => $transactionId,
                    'fecha_procesamiento' => now(),
                    'mensaje_error' => $data['status_message'] ?? $data['transaction']['status_message'] ?? 'Transacción rechazada',
                    'respuesta_pasarela' => $data
                ]);

                // Actualizar compra
                $transaccion->compra->update(['estado' => 'cancelada']);

                // Liberar stock
                $this->liberarStock($transaccion->compra);

                Log::info("Pago rechazado: {$transaccion->referencia_transaccion}");
                break;

            case 'ERROR':
                $transaccion->update([
                    'estado' => 'error',
                    'id_transaccion_pasarela' => $transactionId,
                    'fecha_procesamiento' => now(),
                    'mensaje_error' => $data['error_message'] ?? $data['transaction']['error_message'] ?? 'Error en transacción',
                    'respuesta_pasarela' => $data
                ]);

                // Actualizar compra
                $transaccion->compra->update(['estado' => 'cancelada']);

                // Liberar stock
                $this->liberarStock($transaccion->compra);

                Log::error("Error en pago: {$transaccion->referencia_transaccion}");
                break;

            case 'PENDING':
                // Solo actualizar ID si no lo tiene
                if (!$transaccion->id_transaccion_pasarela) {
                    $transaccion->update([
                        'id_transaccion_pasarela' => $transactionId,
                        'respuesta_pasarela' => $data
                    ]);
                }
                Log::info("Pago pendiente: {$transaccion->referencia_transaccion}");
                break;
        }
    }

    /**
     * Procesar pago de membresía
     */
    private function procesarPagoMembresia($transaccion, $status, $transactionId, $data)
    {
        // Buscar el pago de membresía
        $pagoMembresia = \App\Models\PagoMembresia::where('referencia_pago', $transaccion->referencia_transaccion)->first();
        
        if (!$pagoMembresia) {
            Log::error("Pago de membresía no encontrado para referencia: {$transaccion->referencia_transaccion}");
            return;
        }
        
        // Actualizar transacción
        $transaccion->update([
            'estado' => $status === 'APPROVED' ? 'aprobada' : 'rechazada',
            'id_transaccion_pasarela' => $transactionId,
            'fecha_procesamiento' => now(),
            'respuesta_pasarela' => $data,
            'metodo_pago' => $data['payment_method_type'] ?? $data['transaction']['payment_method_type'] ?? null,
        ]);
        
        switch ($status) {
            case 'APPROVED':
                $pagoMembresia->aprobar($data);
                Log::info("Pago de membresía aprobado: {$transaccion->referencia_transaccion}");
                break;
                
            case 'DECLINED':
            case 'VOIDED':
            case 'ERROR':
                $mensaje = $data['status_message'] ?? $data['transaction']['status_message'] ?? 'Pago rechazado';
                $pagoMembresia->rechazar($mensaje);
                Log::info("Pago de membresía rechazado: {$transaccion->referencia_transaccion}");
                break;
        }
    }

    /**
     * Liberar stock de productos
     */
    private function liberarStock($compra)
    {
        foreach ($compra->items as $item) {
            if ($item->producto && $item->producto->controlar_stock) {
                $stock = $item->variante_producto_id
                    ? $item->producto->stock()->where('variante_producto_id', $item->variante_producto_id)->first()
                    : $item->producto->stockPrincipal;

                if ($stock) {
                    $stock->entrada(
                        $item->cantidad,
                        'devolucion',
                        $compra->numero_compra,
                        'Pago cancelado/rechazado'
                    );
                }
            }
        }
    }
}