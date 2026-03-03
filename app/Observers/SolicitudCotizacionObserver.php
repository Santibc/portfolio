<?php

namespace App\Observers;

use App\Mail\EstadoPedidoCambiado;
use App\Models\SolicitudCotizacion;
use Illuminate\Support\Facades\Mail;

/**
 * Observer para SolicitudCotizacion
 * Envía notificaciones automáticas al cliente cuando cambian estados importantes
 * Fase 7: Portal de Cliente
 */
class SolicitudCotizacionObserver
{
    /**
     * Handle the SolicitudCotizacion "updated" event.
     */
    public function updated(SolicitudCotizacion $solicitud): void
    {
        // Verificar si el cliente tiene cuenta de usuario con email
        $emailCliente = $this->getEmailCliente($solicitud);

        if (!$emailCliente) {
            return;
        }

        // Notificar cambios de estado de envío
        if ($solicitud->isDirty('estado_envio')) {
            $estadoAnterior = $solicitud->getOriginal('estado_envio');
            $estadoNuevo = $solicitud->estado_envio;

            // Solo notificar cambios significativos
            if ($this->debeNotificarCambioEnvio($estadoNuevo)) {
                $this->enviarNotificacion(
                    $emailCliente,
                    $solicitud,
                    'envio',
                    $estadoAnterior ?? SolicitudCotizacion::ENVIO_PENDIENTE,
                    $estadoNuevo
                );
            }
        }

        // Notificar confirmación de pago
        if ($solicitud->isDirty('estado_pago')) {
            $estadoAnterior = $solicitud->getOriginal('estado_pago');
            $estadoNuevo = $solicitud->estado_pago;

            // Solo notificar cuando el pago es confirmado completamente
            if ($estadoNuevo === SolicitudCotizacion::PAGO_PAGADO && $estadoAnterior !== SolicitudCotizacion::PAGO_PAGADO) {
                $this->enviarNotificacion(
                    $emailCliente,
                    $solicitud,
                    'pago',
                    $estadoAnterior ?? SolicitudCotizacion::PAGO_PENDIENTE,
                    $estadoNuevo
                );
            }
        }
    }

    /**
     * Obtener el email del cliente
     */
    private function getEmailCliente(SolicitudCotizacion $solicitud): ?string
    {
        // Primero intentar con la cuenta de usuario
        if ($solicitud->cliente && $solicitud->cliente->user && $solicitud->cliente->user->email) {
            return $solicitud->cliente->user->email;
        }

        // Si no tiene cuenta, usar el email del cliente directamente
        if ($solicitud->cliente && $solicitud->cliente->email) {
            return $solicitud->cliente->email;
        }

        return null;
    }

    /**
     * Determinar si se debe notificar un cambio de estado de envío
     */
    private function debeNotificarCambioEnvio(string $estadoNuevo): bool
    {
        // Notificar solo en estados importantes para el cliente
        return in_array($estadoNuevo, [
            SolicitudCotizacion::ENVIO_PREPARANDO,
            SolicitudCotizacion::ENVIO_DESPACHADO,
            SolicitudCotizacion::ENVIO_EN_TRANSITO,
            SolicitudCotizacion::ENVIO_ENTREGADO,
        ]);
    }

    /**
     * Enviar la notificación por email
     */
    private function enviarNotificacion(
        string $email,
        SolicitudCotizacion $solicitud,
        string $tipoEstado,
        string $estadoAnterior,
        string $estadoNuevo
    ): void {
        try {
            // Recargar relaciones necesarias para el email
            $solicitud->loadMissing(['cliente', 'items']);

            $ccEmails = $solicitud->cliente ? $solicitud->cliente->emails_adicionales_array : [];
            Mail::to($email)
                ->cc($ccEmails)
                ->queue(new EstadoPedidoCambiado($solicitud, $tipoEstado, $estadoAnterior, $estadoNuevo));
        } catch (\Exception $e) {
            // Log el error pero no interrumpir la operación
            \Log::error("Error enviando notificación de estado de pedido: " . $e->getMessage(), [
                'solicitud_id' => $solicitud->id,
                'email' => $email,
                'tipo_estado' => $tipoEstado,
            ]);
        }
    }
}
