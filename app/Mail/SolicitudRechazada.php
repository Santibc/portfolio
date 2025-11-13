<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SolicitudCotizacion;

class SolicitudRechazada extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SolicitudCotizacion $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Solicitud de Cotización #' . $this->solicitud->numero_solicitud . ' - Rechazada')
                    ->view('emails.solicitud-rechazada');
    }
}
