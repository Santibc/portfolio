<?php

namespace App\Mail;

use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacturaEnviadaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Factura $factura;

    /**
     * Create a new message instance.
     */
    public function __construct(Factura $factura)
    {
        $this->factura = $factura;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Factura {$this->factura->numero} - Manzer Agroforestal",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.factura-enviada',
            with: [
                'factura' => $this->factura,
                'cliente' => $this->factura->cliente,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Adjuntar PDF si existe
        if ($this->factura->pdf_path && file_exists(public_path($this->factura->pdf_path))) {
            $attachments[] = Attachment::fromPath(public_path($this->factura->pdf_path))
                ->as("Factura_{$this->factura->numero}.pdf")
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
