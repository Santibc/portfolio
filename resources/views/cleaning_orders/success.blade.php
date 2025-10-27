@extends('landing_page.layout')

@section('content')

<section style="padding: 160px 0 100px 0;" class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Success Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">

                        <!-- Success Icon -->
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                        </div>

                        <!-- Title -->
                        <h2 class="mb-3">Payment Successful!</h2>
                        <p class="lead text-muted mb-4">
                            Thank you for your order. Your cleaning service has been scheduled.
                        </p>

                        <!-- Order Details -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">Order Details</h5>

                                <div class="row text-start mb-2">
                                    <div class="col-6">
                                        <strong>Order Number:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ $order->order_number }}
                                    </div>
                                </div>

                                <div class="row text-start mb-2">
                                    <div class="col-6">
                                        <strong>Service Type:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ ucfirst(str_replace('_', ' ', $order->service_type)) }}
                                    </div>
                                </div>

                                <div class="row text-start mb-2">
                                    <div class="col-6">
                                        <strong>Scheduled Date:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ $order->preferred_date->format('M d, Y') }} at {{ $order->preferred_time }}
                                    </div>
                                </div>

                                <div class="row text-start mb-2">
                                    <div class="col-6">
                                        <strong>Location:</strong>
                                    </div>
                                    <div class="col-6">
                                        {{ $order->street_address }}
                                        @if($order->district)
                                            <br>{{ $order->district->name }}, {{ $order->district->state }}
                                        @endif
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="row text-start">
                                    <div class="col-6">
                                        <strong class="h5">Total Paid:</strong>
                                    </div>
                                    <div class="col-6">
                                        <strong class="h5 text-success">${{ number_format($order->total, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation Email Notice -->
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-envelope-fill me-2"></i>
                            <div class="text-start">
                                A confirmation email has been sent to <strong>{{ $order->email }}</strong>
                            </div>
                        </div>

                        <!-- What's Next -->
                        <div class="card border-primary mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">What Happens Next?</h5>
                                <div class="text-start">
                                    <p class="mb-2">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        We'll send you a confirmation email with all the details
                                    </p>
                                    <p class="mb-2">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        Our team will contact you 24 hours before the scheduled service
                                    </p>
                                    <p class="mb-2">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        You can reschedule or modify your service by contacting us
                                    </p>
                                    <p class="mb-0">
                                        <i class="bi bi-check-circle text-primary me-2"></i>
                                        We'll arrive on time and provide exceptional service
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <p class="text-muted mb-4">
                            Questions about your order? Contact us at
                            <a href="mailto:{{ $layoutConfig->email ?? 'info@cleanme.com' }}">
                                {{ $layoutConfig->email ?? 'info@cleanme.com' }}
                            </a>
                        </p>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('welcome') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-house-fill me-2"></i>Return Home
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
