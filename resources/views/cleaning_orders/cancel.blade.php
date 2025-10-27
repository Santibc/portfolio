@extends('landing_page.layout')

@section('content')

<section style="padding: 160px 0 100px 0;" class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Cancel Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">

                        <!-- Cancel Icon -->
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill text-warning" style="font-size: 5rem;"></i>
                        </div>

                        <!-- Title -->
                        <h2 class="mb-3">Payment Cancelled</h2>
                        <p class="lead text-muted mb-4">
                            Your payment was not completed. No charges have been made.
                        </p>

                        @if($order)
                        <!-- Order Reference -->
                        <div class="alert alert-info mb-4">
                            <strong>Order Reference:</strong> {{ $order->order_number }}
                            <br>
                            <small class="text-muted">This order is still pending payment</small>
                        </div>
                        @endif

                        <!-- What Happened -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">What Happened?</h5>
                                <p class="text-start mb-0">
                                    You cancelled the payment process or the payment could not be completed.
                                    Don't worry - no charges were made to your card. You can try again at any time.
                                </p>
                            </div>
                        </div>

                        <!-- Why This Might Happen -->
                        <div class="accordion mb-4" id="cancelReasons">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reasonsCollapse">
                                        Common Reasons for Payment Issues
                                    </button>
                                </h2>
                                <div id="reasonsCollapse" class="accordion-collapse collapse" data-bs-parent="#cancelReasons">
                                    <div class="accordion-body text-start">
                                        <ul class="mb-0">
                                            <li>Payment was intentionally cancelled</li>
                                            <li>Insufficient funds in the account</li>
                                            <li>Card details were entered incorrectly</li>
                                            <li>Bank declined the transaction</li>
                                            <li>Internet connection was lost</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-3">
                            <a href="{{ route('services.calculator') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                            </a>
                            <a href="{{ route('welcome') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-house-fill me-2"></i>Return Home
                            </a>
                        </div>

                        <!-- Need Help -->
                        <div class="card border-primary">
                            <div class="card-body">
                                <h5 class="mb-3">Need Help?</h5>
                                <p class="mb-3">
                                    If you're experiencing issues with payment, please contact our support team.
                                    We're here to help!
                                </p>
                                <a href="{{ route('contacto') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-headset me-2"></i>Contact Support
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
