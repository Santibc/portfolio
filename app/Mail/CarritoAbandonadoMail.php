<?php

namespace App\Mail;

use App\Models\Carrito;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CarritoAbandonadoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Carrito $carrito;
    public int $numeroRecordatorio;
    public string $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Carrito $carrito, int $numeroRecordatorio, string $token)
    {
        $this->carrito = $carrito;
        $this->numeroRecordatorio = $numeroRecordatorio;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $asuntos = [
            1 => '¡Olvidaste algo! Tu carrito te espera 🛒',
            2 => '¡Tus flores siguen esperándote! 🌸',
            3 => '¡Última oportunidad! 10% de descuento en tu carrito 🎁',
        ];

        return new Envelope(
            subject: $asuntos[$this->numeroRecordatorio] ?? 'Tu carrito te espera',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.carrito-abandonado',
            with: [
                'carrito' => $this->carrito,
                'empresa' => $this->carrito->empresa,
                'items' => $this->carrito->items ?? [],
                'total' => $this->carrito->total,
                'numeroRecordatorio' => $this->numeroRecordatorio,
                'urlRecuperacion' => $this->generarUrlRecuperacion(),
                'nombreCliente' => $this->carrito->nombre_cliente ?? 'Cliente',
                'tieneDescuento' => $this->numeroRecordatorio >= 3,
                'porcentajeDescuento' => 10,
            ],
        );
    }

    /**
     * Generar URL de recuperación del carrito
     */
    private function generarUrlRecuperacion(): string
    {
        return route('carrito.recuperar', [
            'token' => $this->token,
        ]);
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
