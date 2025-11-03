<?php

namespace App\Mail;

use App\Models\STOrdenServicio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdenServicioEstadoCambiado extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;
    public $estadoAnterior;
    public $estadoNuevo;
    public $observaciones;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(STOrdenServicio $orden, $estadoAnterior, $estadoNuevo, $observaciones = null)
    {
        $this->orden = $orden;
        $this->estadoAnterior = $estadoAnterior;
        $this->estadoNuevo = $estadoNuevo;
        $this->observaciones = $observaciones;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Actualización de Orden - ' . $this->orden->numero_orden,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.orden-servicio-estado-cambiado',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
