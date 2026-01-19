<?php

namespace App\Mail;

use App\Models\SolicitudCotizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email de alerta enviado a vendedor y admin cuando una cotización es aprobada
 */
class AlertaCotizacionAceptada extends Mailable
{
    use Queueable, SerializesModels;

    public SolicitudCotizacion $solicitud;
    public string $tipoDestino; // 'vendedor' o 'admin'

    /**
     * Create a new message instance.
     */
    public function __construct(SolicitudCotizacion $solicitud, string $tipoDestino = 'vendedor')
    {
        $this->solicitud = $solicitud;
        $this->tipoDestino = $tipoDestino;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $asunto = $this->tipoDestino === 'admin'
            ? "Nueva cotización aprobada - {$this->solicitud->numero_solicitud}"
            : "Tu cotización {$this->solicitud->numero_solicitud} fue aprobada";

        return $this->subject($asunto)
                    ->view('emails.alerta-cotizacion');
    }
}
