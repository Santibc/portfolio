<?php

namespace App\Mail;

use App\Models\CleaningOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRequestAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public CleaningOrder $order;

    public function __construct(CleaningOrder $order)
    {
        $this->order = $order;
    }

    public function envelope()
    {
        return new Envelope(
            subject: "New booking request — {$this->order->order_number}",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.booking-request-admin',
            with: ['order' => $this->order]
        );
    }

    public function attachments()
    {
        return [];
    }
}
