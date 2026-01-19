<?php

namespace App\Mail;

use App\Models\SolicitudCotizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email de notificación al cliente cuando cambia el estado de su pedido
 * Fase 7: Portal de Cliente
 */
class EstadoPedidoCambiado extends Mailable
{
    use Queueable, SerializesModels;

    public SolicitudCotizacion $solicitud;
    public string $tipoEstado; // 'cotizacion', 'pago', 'envio'
    public string $estadoAnterior;
    public string $estadoNuevo;

    /**
     * Create a new message instance.
     */
    public function __construct(
        SolicitudCotizacion $solicitud,
        string $tipoEstado,
        string $estadoAnterior,
        string $estadoNuevo
    ) {
        $this->solicitud = $solicitud;
        $this->tipoEstado = $tipoEstado;
        $this->estadoAnterior = $estadoAnterior;
        $this->estadoNuevo = $estadoNuevo;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $asuntos = [
            'cotizacion' => "Actualización de tu cotización {$this->solicitud->numero_solicitud}",
            'pago' => "Confirmación de pago - Pedido {$this->solicitud->numero_solicitud}",
            'envio' => $this->getAsuntoEnvio(),
        ];

        return $this->subject($asuntos[$this->tipoEstado] ?? "Actualización de pedido {$this->solicitud->numero_solicitud}")
                    ->view('emails.estado-pedido-cambiado');
    }

    /**
     * Obtener asunto específico para cambios de envío
     */
    private function getAsuntoEnvio(): string
    {
        return match($this->estadoNuevo) {
            SolicitudCotizacion::ENVIO_PREPARANDO => "Tu pedido {$this->solicitud->numero_solicitud} está siendo preparado",
            SolicitudCotizacion::ENVIO_DESPACHADO => "¡Tu pedido {$this->solicitud->numero_solicitud} ha sido despachado!",
            SolicitudCotizacion::ENVIO_EN_TRANSITO => "Tu pedido {$this->solicitud->numero_solicitud} está en camino",
            SolicitudCotizacion::ENVIO_ENTREGADO => "Tu pedido {$this->solicitud->numero_solicitud} ha sido entregado",
            default => "Actualización de envío - {$this->solicitud->numero_solicitud}",
        };
    }

    /**
     * Obtener el mensaje descriptivo del cambio
     */
    public function getMensajeCambio(): string
    {
        if ($this->tipoEstado === 'envio') {
            return match($this->estadoNuevo) {
                SolicitudCotizacion::ENVIO_PREPARANDO => 'Estamos preparando tu pedido para envío.',
                SolicitudCotizacion::ENVIO_DESPACHADO => '¡Tu pedido ha sido despachado! Pronto lo recibirás.',
                SolicitudCotizacion::ENVIO_EN_TRANSITO => 'Tu pedido está en camino hacia ti.',
                SolicitudCotizacion::ENVIO_ENTREGADO => '¡Tu pedido ha sido entregado con éxito!',
                default => 'El estado de tu envío ha sido actualizado.',
            };
        }

        if ($this->tipoEstado === 'pago') {
            return 'Tu pago ha sido confirmado. Gracias por tu compra.';
        }

        return 'El estado de tu pedido ha sido actualizado.';
    }
}
