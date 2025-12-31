<?php

namespace App\Observers;

use App\Models\Compra;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class CompraObserver
{
    /**
     * Handle the Compra "updated" event.
     */
    public function updated(Compra $compra): void
    {
        // Solo procesar si el estado cambió
        if (!$compra->wasChanged('estado')) {
            return;
        }

        $estadoAnterior = $compra->getOriginal('estado');
        $estadoNuevo = $compra->estado;

        Log::info('Compra estado cambiado', [
            'compra_id' => $compra->id,
            'de' => $estadoAnterior,
            'a' => $estadoNuevo,
        ]);

        // Instanciar servicio de WhatsApp
        $whatsapp = new WhatsAppService($compra->empresa);

        // Solo enviar si WhatsApp está configurado
        if (!$whatsapp->isConfigured()) {
            return;
        }

        try {
            switch ($estadoNuevo) {
                case 'confirmado':
                case 'pagado':
                    // Notificar confirmación de pedido
                    if (in_array($estadoAnterior, ['pendiente', 'procesando'])) {
                        $whatsapp->notificarConfirmacionPedido($compra);
                    }
                    break;

                case 'enviado':
                case 'en_camino':
                    // Notificar que el pedido está en camino
                    $whatsapp->notificarEnvio($compra);
                    break;

                case 'entregado':
                    // Notificar entrega exitosa
                    $this->notificarEntrega($whatsapp, $compra);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error enviando notificación WhatsApp', [
                'compra_id' => $compra->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notificar entrega exitosa
     */
    private function notificarEntrega(WhatsAppService $whatsapp, Compra $compra): void
    {
        $telefono = $compra->cliente->telefono ?? $compra->telefono;

        if (!$telefono) {
            return;
        }

        $mensaje = "✅ *¡Tu pedido ha sido entregado!*\n\n";
        $mensaje .= "Pedido: *#{$compra->numero_compra}*\n\n";
        $mensaje .= "Esperamos que disfrutes tus flores 🌸\n\n";
        $mensaje .= "¿Te gustó nuestro servicio? Déjanos tu opinión.\n\n";
        $mensaje .= "_{$compra->empresa->nombre}_";

        $whatsapp->enviarTexto($telefono, $mensaje);
    }
}
