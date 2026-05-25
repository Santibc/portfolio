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
        $layoutConfig = \App\Models\LandingLayoutConfig::first();
        $subjectTemplate = $layoutConfig->customer_email_subject ?? 'Booking received — {reference}';
        $subject = str_replace('{reference}', $this->order->order_number, $subjectTemplate);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.booking-request-customer',
            with: [
                'order' => $this->order,
                'layoutConfig' => \App\Models\LandingLayoutConfig::first(),
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
