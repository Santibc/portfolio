@extends('landing_page.layout')

@section('content')

<section class="section" style="padding: 160px 0 100px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-body text-center p-5">
                        <div style="font-size: 4rem; color: #28a745;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                        <h2 class="mt-3 mb-2">Booking received!</h2>
                        <p class="text-muted">
                            Thanks {{ $order->first_name }}. Your booking request has been registered.<br>
                            We'll contact you shortly to confirm date, final pricing and payment.
                        </p>

                        <div class="alert alert-light border mt-4 text-start">
                            <div class="row">
                                <div class="col-md-6"><small class="text-muted">Reference</small><div class="fw-bold">{{ $order->order_number }}</div></div>
                                <div class="col-md-6"><small class="text-muted">Status</small><div class="fw-bold text-warning">Pending confirmation</div></div>
                                <div class="col-md-6 mt-3"><small class="text-muted">Service date</small><div>{{ \Carbon\Carbon::parse($order->preferred_date)->format('D, d M Y') }} at {{ $order->preferred_time }}</div></div>
                                <div class="col-md-6 mt-3"><small class="text-muted">Estimated total</small><div>${{ number_format($order->total, 2) }} {{ $order->currency }}</div></div>
                                <div class="col-12 mt-3"><small class="text-muted">Address</small><div>{{ $order->full_address }}</div></div>
                            </div>
                        </div>

                        <p class="small text-muted mb-4">
                            <i class="bi bi-envelope"></i> A copy of these details has been sent to <strong>{{ $order->email }}</strong>.
                        </p>

                        <a href="{{ route('welcome') }}" class="btn btn-primary">
                            <i class="bi bi-house"></i> Back to home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
