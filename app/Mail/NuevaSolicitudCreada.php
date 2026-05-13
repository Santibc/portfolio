<?php

namespace App\Mail;

use App\Models\SolicitudCotizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudCreada extends Mailable
{
    use Queueable, SerializesModels;

    public SolicitudCotizacion $solicitud;
    public string $detalleUrl;

    public function __construct(SolicitudCotizacion $solicitud)
    {
        $this->solicitud = $solicitud;
        $this->detalleUrl = route('solicitudes') . '#' . $solicitud->id;
    }

    public function build()
    {
        return $this->subject('Nueva solicitud de cotización ' . $this->solicitud->codigo_corto)
                    ->view('emails.nueva-solicitud');
    }
}
