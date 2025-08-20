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
        // Log del webhook recibido
        Log::info('Webhook Wompi recibido', $request->all());

        try {
            // Verificar la firma del webhook (implementar según documentación de Wompi)
            if (!$this->verificarFirmaWompi($request)) {
                Log::warning('Webhook Wompi con firma inválida');
                return response()->json(['error' => 'Firma inválida'], 401);
            }

            // Obtener datos del evento
            $evento = $request->input('event');
            $datos = $request->input('data');
            
            // Buscar la transacción
            $referencia = $datos['reference'] ?? null;
            if (!$referencia) {
                return response()->json(['error' => 'Referencia no encontrada'], 400);
            }

            $transaccion = TransaccionPago::where('referencia_transaccion', $referencia)->first();
            if (!$transaccion) {
                Log::warning('Transacción no encontrada para referencia: ' . $referencia);
                return response()->json(['error' => 'Transacción no encontrada'], 404);
            }

            // Registrar el evento
            $transaccion->registrarEvento($evento, $datos, $request->ip());

            // Procesar según el tipo de evento
            switch ($evento) {
                case 'transaction.updated':
                    $this->procesarActualizacionTransaccion($transaccion, $datos);
                    break;
                    
                case 'payment_link.paid':
                    $this->procesarPagoCompletado($transaccion, $datos);
                    break;
                    
                default:
                    Log::info('Evento Wompi no manejado: ' . $evento);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error procesando webhook Wompi: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    /**
     * Verificar firma del webhook de Wompi
     */
    private function verificarFirmaWompi(Request $request)
    {
        $config = ConfiguracionPasarela::obtenerConfiguracionActiva('wompi');
        if (!$config || !$config->event_key) {
            return false;
        }

        // Obtener la firma del header
        $firmaRecibida = $request->header('X-Event-Signature');
        if (!$firmaRecibida) {
            return false;
        }

        // Calcular la firma esperada
        $payload = $request->getContent();
        $firmaEsperada = hash_hmac('sha256', $payload, $config->event_key);

        return hash_equals($firmaEsperada, $firmaRecibida);
    }

    /**
     * Procesar actualización de transacción
     */
    private function procesarActualizacionTransaccion($transaccion, $datos)
    {
        $estado = $datos['status'] ?? null;
        
        switch ($estado) {
            case 'APPROVED':
                $transaccion->update([
                    'estado' => 'aprobada',
                    'id_transaccion_pasarela' => $datos['id'],
                    'metodo_pago' => $datos['payment_method_type'] ?? null,
                    'fecha_procesamiento' => now(),
                    'respuesta_pasarela' => $datos
                ]);
                
                // Actualizar compra
                $transaccion->compra->update(['estado' => 'pagada']);
                $transaccion->compra->generarComision();
                
                // Enviar email de confirmación
                // TODO: Implementar envío de email
                
                break;
                
            case 'DECLINED':
            case 'VOIDED':
                $transaccion->update([
                    'estado' => 'rechazada',
                    'mensaje_error' => $datos['status_message'] ?? 'Transacción rechazada',
                    'respuesta_pasarela' => $datos
                ]);
                
                // Liberar stock si estaba reservado
                $this->liberarStockCompra($transaccion->compra);
                
                break;
                
            case 'ERROR':
                $transaccion->update([
                    'estado' => 'error',
                    'mensaje_error' => $datos['error_message'] ?? 'Error en la transacción',
                    'respuesta_pasarela' => $datos
                ]);
                
                // Liberar stock si estaba reservado
                $this->liberarStockCompra($transaccion->compra);
                
                break;
        }
    }

    /**
     * Procesar pago completado
     */
    private function procesarPagoCompletado($transaccion, $datos)
    {
        $this->procesarActualizacionTransaccion($transaccion, $datos);
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
                    // Devolver el stock
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