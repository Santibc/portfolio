<?php

namespace App\Http\Controllers;

use App\Models\TransaccionPago;
use App\Services\WompiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Manejar webhook de Wompi
     */
    public function wompi(Request $request)
    {
        Log::info('Webhook Wompi recibido', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        try {
            // Verificar la firma del webhook
            $wompiService = new WompiService();
            $firmaRecibida = $request->header('X-Event-Signature');
            $payload = $request->getContent();
            
            if (!$wompiService->verificarFirmaWebhook($payload, $firmaRecibida)) {
                Log::warning('Webhook Wompi con firma inválida');
                return response()->json(['error' => 'Transacción no encontrada'], 404);
            }

            // Registrar el evento
            $transaccionLocal->registrarEvento($evento, $transaccion, $request->ip());

            // Procesar según el tipo de evento
            switch ($evento) {
                case 'transaction.updated':
                    $this->procesarActualizacionTransaccion($transaccionLocal, $transaccion);
                    break;
                    
                default:
                    Log::info('Evento Wompi no manejado: ' . $evento);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook Wompi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    /**
     * Procesar actualización de transacción
     */
    private function procesarActualizacionTransaccion($transaccionLocal, $datosWompi)
    {
        // No procesar si ya fue procesada
        if (in_array($transaccionLocal->estado, ['aprobada', 'rechazada', 'error'])) {
            Log::info('Transacción ya procesada: ' . $transaccionLocal->referencia_transaccion);
            return;
        }

        $estado = $datosWompi['status'] ?? null;
        
        switch ($estado) {
            case 'APPROVED':
                $transaccionLocal->update([
                    'estado' => 'aprobada',
                    'id_transaccion_pasarela' => $datosWompi['id'],
                    'metodo_pago' => $datosWompi['payment_method_type'] ?? null,
                    'fecha_procesamiento' => now(),
                    'respuesta_pasarela' => $datosWompi,
                    'codigo_autorizacion' => $datosWompi['authorization_code'] ?? null
                ]);
                
                // Actualizar compra
                $transaccionLocal->compra->update(['estado' => 'pagada']);
                
                // Generar comisión
                $transaccionLocal->compra->generarComision();
                
                // TODO: Enviar email de confirmación
                
                Log::info('Transacción aprobada: ' . $transaccionLocal->referencia_transaccion);
                break;
                
            case 'DECLINED':
            case 'VOIDED':
                $transaccionLocal->update([
                    'estado' => 'rechazada',
                    'id_transaccion_pasarela' => $datosWompi['id'],
                    'mensaje_error' => $datosWompi['status_message'] ?? 'Transacción rechazada',
                    'respuesta_pasarela' => $datosWompi
                ]);
                
                // Actualizar estado de la compra
                $transaccionLocal->compra->update(['estado' => 'cancelada']);
                
                // Liberar stock si estaba reservado
                $this->liberarStockCompra($transaccionLocal->compra);
                
                Log::info('Transacción rechazada: ' . $transaccionLocal->referencia_transaccion);
                break;
                
            case 'ERROR':
                $transaccionLocal->update([
                    'estado' => 'error',
                    'id_transaccion_pasarela' => $datosWompi['id'],
                    'mensaje_error' => $datosWompi['error_message'] ?? 'Error en la transacción',
                    'respuesta_pasarela' => $datosWompi
                ]);
                
                // Actualizar estado de la compra
                $transaccionLocal->compra->update(['estado' => 'cancelada']);
                
                // Liberar stock
                $this->liberarStockCompra($transaccionLocal->compra);
                
                Log::error('Error en transacción: ' . $transaccionLocal->referencia_transaccion);
                break;
                
            case 'PENDING':
                // Solo actualizar si cambia de otro estado a pendiente
                $transaccionLocal->update([
                    'id_transaccion_pasarela' => $datosWompi['id'],
                    'respuesta_pasarela' => $datosWompi
                ]);
                
                Log::info('Transacción pendiente: ' . $transaccionLocal->referencia_transaccion);
                break;
        }
    }

    /**
     * Liberar stock de una compra cancelada/rechazada
     */
    private function liberarStockCompra($compra)
    {
        foreach ($compra->items as $item) {
            $producto = $item->producto;
            
            if ($producto && $producto->controlar_stock) {
                $stock = $item->variante_producto_id 
                    ? $producto->stock()->where('variante_producto_id', $item->variante_producto_id)->first()
                    : $producto->stockPrincipal;
                
                if ($stock) {
                    // Devolver el stock usando el método entrada()
                    $stock->entrada(
                        $item->cantidad, 
                        'devolucion', 
                        $compra->numero_compra,
                        'Pago rechazado/cancelado'
                    );
                }
            }
        }
    }
}