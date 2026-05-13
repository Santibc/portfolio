<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New booking</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333; max-width:680px; margin:0 auto; padding:20px;">
    <div style="background:#1d4f7c; color:white; padding:14px 20px; border-radius:6px 6px 0 0;">
        <h2 style="margin:0;">New booking request — {{ $order->order_number }}</h2>
    </div>

    <div style="border:1px solid #ddd; border-top:0; padding:20px; border-radius:0 0 6px 6px;">

        <h3 style="margin-top:0;">Customer</h3>
        <table style="width:100%; border-collapse:collapse;" cellpadding="5">
            <tr><td><strong>Name:</strong></td><td>{{ $order->full_name }}</td></tr>
            <tr><td><strong>Email:</strong></td><td><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></td></tr>
            <tr><td><strong>Phone:</strong></td><td><a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></td></tr>
        </table>

        <h3>Service location</h3>
        <table style="width:100%; border-collapse:collapse;" cellpadding="5">
            <tr><td><strong>Address:</strong></td><td>{{ $order->full_address }}</td></tr>
            <tr><td><strong>Suburb / State / Postcode:</strong></td><td>{{ $order->suburb }} {{ $order->state }} {{ $order->postcode }}</td></tr>
            @if($order->latitude && $order->longitude)
                <tr><td><strong>Map:</strong></td><td><a href="https://www.google.com/maps/search/?api=1&query={{ $order->latitude }},{{ $order->longitude }}">Open in Google Maps</a></td></tr>
            @endif
        </table>

        <h3>Schedule</h3>
        <table style="width:100%; border-collapse:collapse;" cellpadding="5">
            <tr><td><strong>Preferred date:</strong></td><td>{{ \Carbon\Carbon::parse($order->preferred_date)->format('D, d M Y') }} at {{ $order->preferred_time }}</td></tr>
            <tr><td><strong>Date flexible:</strong></td><td>{{ $order->date_flexible ? 'Yes' : 'No' }}</td></tr>
            <tr><td><strong>Time flexible:</strong></td><td>{{ $order->time_flexible ? 'Yes' : 'No' }}</td></tr>
            <tr><td><strong>Parking:</strong></td><td>{{ $order->parking ?? '—' }}</td></tr>
            <tr><td><strong>Property access:</strong></td><td>{{ $order->property_access ?? '—' }}{{ $order->access_notes ? ' — '.$order->access_notes : '' }}</td></tr>
        </table>

        <h3>Service details</h3>
        <table style="width:100%; border-collapse:collapse;" cellpadding="5">
            <tr><td><strong>Cleaners × hours:</strong></td><td>{{ $order->num_cleaners }} × {{ $order->num_hours }}</td></tr>
            <tr><td><strong>Service type:</strong></td><td>{{ ucfirst($order->service_type) }}</td></tr>
            <tr><td><strong>Rooms:</strong></td><td>{{ $order->num_bedrooms }} bed / {{ $order->num_bathrooms }} bath / {{ $order->num_kitchens }} kitchen{{ $order->num_other_rooms ? ' / '.$order->num_other_rooms.' other ('.($order->other_rooms_desc ?? '').')' : '' }}</td></tr>
            @if(is_array($order->extras) && count($order->extras))
                <tr><td><strong>Extras:</strong></td><td>
                    @foreach($order->extras as $ex)
                        {{ $ex['name'] ?? '—' }} (${{ number_format($ex['price'] ?? 0, 2) }})<br>
                    @endforeach
                </td></tr>
            @endif
            <tr><td><strong>Estimated total:</strong></td><td><strong>${{ number_format($order->total, 2) }} {{ $order->currency }}</strong></td></tr>
            @if($order->coupon_code)
                <tr><td><strong>Coupon:</strong></td><td>{{ $order->coupon_code }} (-${{ number_format($order->discount_amount, 2) }})</td></tr>
            @endif
        </table>

        <p style="margin-top:25px;">
            <a href="{{ route('admin.cleaning-orders.show', $order) }}"
               style="background:#1d4f7c; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; display:inline-block;">
                Open in admin panel →
            </a>
        </p>
    </div>
</body>
</html>
