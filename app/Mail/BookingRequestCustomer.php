<?php

namespace App\Mail;

use App\Models\CleaningOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRequestCustomer extends Mailable
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
            subject: "Booking received — {$this->order->order_number}",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.booking-request-customer',
            with: ['order' => $this->order]
        );
    }

    public function attachments()
    {
        return [];
    }
}
