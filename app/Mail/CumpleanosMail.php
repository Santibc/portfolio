<?php

namespace App\Mail;

use App\Models\CumpleanosConfiguracion;
use App\Models\Trabajador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CumpleanosMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Trabajador $trabajador;
    public CumpleanosConfiguracion $config;

    public function __construct(Trabajador $trabajador, CumpleanosConfiguracion $config)
    {
        $this->trabajador = $trabajador;
        $this->config = $config;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->config->resolverAsunto($this->trabajador),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cumpleanos',
            with: [
                'trabajador' => $this->trabajador,
                'cuerpoHtml' => $this->config->resolverCuerpo($this->trabajador),
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->config->adjunto_path && file_exists(public_path($this->config->adjunto_path))) {
            $attachments[] = Attachment::fromPath(public_path($this->config->adjunto_path))
                ->as($this->config->adjunto_nombre_original ?? basename($this->config->adjunto_path))
                ->withMime(mime_content_type(public_path($this->config->adjunto_path)));
        }

        return $attachments;
    }
}
