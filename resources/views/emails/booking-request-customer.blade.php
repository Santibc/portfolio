<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $layoutConfig->customer_email_subject ? str_replace('{reference}', $order->order_number, $layoutConfig->customer_email_subject) : 'Booking received' }}</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333; max-width:600px; margin:0 auto; padding:20px;">
    <h2 style="color:#1d4f7c;">Hi {{ $order->first_name }},</h2>

    <p>{!! nl2br(e($layoutConfig->customer_email_intro ?? 'Thanks for booking your cleaning service with us — we have received your request and one of our team will contact you shortly to confirm the date, final pricing and payment method.')) !!}</p>

    <h3 style="color:#1d4f7c; border-bottom:1px solid #ddd; padding-bottom:6px;">Booking summary</h3>

    <table style="width:100%; border-collapse:collapse;" cellpadding="6">
        <tr><td><strong>Reference:</strong></td><td>{{ $order->order_number }}</td></tr>
        <tr><td><strong>Service date:</strong></td><td>{{ \Carbon\Carbon::parse($order->preferred_date)->format('D, d M Y') }} at {{ $order->preferred_time }}</td></tr>
        <tr><td><strong>Address:</strong></td><td>{{ $order->full_address }}</td></tr>
        <tr><td><strong>Bedrooms:</strong></td><td>{{ $order->num_bedrooms }}</td></tr>
        <tr><td><strong>Bathrooms:</strong></td><td>{{ $order->num_bathrooms }}</td></tr>
        <tr><td><strong>Kitchens:</strong></td><td>{{ $order->num_kitchens }}</td></tr>
        @if($order->num_other_rooms)
            <tr><td><strong>Other rooms:</strong></td><td>{{ $order->num_other_rooms }} {{ $order->other_rooms_desc ? '('.$order->other_rooms_desc.')' : '' }}</td></tr>
        @endif
        <tr><td><strong>Cleaners / Hours:</strong></td><td>{{ $order->num_cleaners }} cleaner(s) × {{ $order->num_hours }} hour(s)</td></tr>
        <tr><td><strong>Service type:</strong></td><td>{{ ucfirst($order->service_type) }}</td></tr>
        <tr><td><strong>Estimated total:</strong></td><td><strong>${{ number_format($order->total, 2) }} {{ $order->currency }}</strong></td></tr>
    </table>

    <p style="margin-top:20px; padding:12px; background:#f2f7fb; border-left:4px solid #1d4f7c;">
        <strong>{{ $layoutConfig->customer_email_next_title ?? 'What happens next?' }}</strong><br>
        {!! nl2br(e($layoutConfig->customer_email_next_text ?? "We'll review your booking and contact you within 24 hours to confirm everything. No payment has been taken at this point.")) !!}
    </p>

    <p style="margin-top:30px; color:#777; font-size:13px;">
        {!! nl2br(e($layoutConfig->customer_email_footer_text ?? 'If you have any questions reply to this email or call us.')) !!}<br>
        — {{ $layoutConfig->customer_email_signature ?? config('app.name') }}
    </p>
</body>
</html>
