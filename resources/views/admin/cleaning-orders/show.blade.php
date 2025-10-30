<x-app-layout>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Order #{{ $cleaningOrder->order_number }}</h2>
                    <p class="text-muted mb-0">
                        Ordered on {{ $cleaningOrder->created_at->format('M d, Y \a\t h:i A') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.cleaning-orders.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">

            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong><br>{{ $cleaningOrder->full_name }}</p>
                            <p><strong>Email:</strong><br>
                                <a href="mailto:{{ $cleaningOrder->email }}">{{ $cleaningOrder->email }}</a>
                            </p>
                            <p><strong>Phone:</strong><br>
                                <a href="tel:{{ $cleaningOrder->phone }}">{{ $cleaningOrder->phone }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Service Address:</strong><br>
                                {{ $cleaningOrder->full_address }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Details</h6>
                </div>
                <div class="card-body">
                    <!-- Room Details -->
                    @if($cleaningOrder->num_bathrooms || $cleaningOrder->num_bedrooms || $cleaningOrder->num_kitchens || $cleaningOrder->other_rooms)
                        <div class="mb-3">
                            <p class="mb-2"><strong>Room Details:</strong></p>
                            <div class="row">
                                @if($cleaningOrder->num_bathrooms)
                                    <div class="col-md-3 mb-2">
                                        <i class="bi bi-water text-primary"></i>
                                        <strong>{{ $cleaningOrder->num_bathrooms }}</strong> Bathroom{{ $cleaningOrder->num_bathrooms > 1 ? 's' : '' }}
                                    </div>
                                @endif
                                @if($cleaningOrder->num_bedrooms)
                                    <div class="col-md-3 mb-2">
                                        <i class="bi bi-door-closed text-primary"></i>
                                        <strong>{{ $cleaningOrder->num_bedrooms }}</strong> Bedroom{{ $cleaningOrder->num_bedrooms > 1 ? 's' : '' }}
                                    </div>
                                @endif
                                @if($cleaningOrder->num_kitchens)
                                    <div class="col-md-3 mb-2">
                                        <i class="bi bi-egg-fried text-primary"></i>
                                        <strong>{{ $cleaningOrder->num_kitchens }}</strong> Kitchen{{ $cleaningOrder->num_kitchens > 1 ? 's' : '' }}
                                    </div>
                                @endif
                                @if($cleaningOrder->num_other_rooms || $cleaningOrder->other_rooms_desc || $cleaningOrder->other_rooms)
                                    <div class="col-md-3 mb-2">
                                        <i class="bi bi-plus-circle text-primary"></i>
                                        @if($cleaningOrder->num_other_rooms)
                                            <strong>{{ $cleaningOrder->num_other_rooms }}</strong> Other
                                        @endif
                                        @if($cleaningOrder->other_rooms_desc)
                                            ({{ $cleaningOrder->other_rooms_desc }})
                                        @elseif($cleaningOrder->other_rooms)
                                            ({{ $cleaningOrder->other_rooms }})
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr>
                    @endif

                    <!-- Cleaners & Hours -->
                    @if($cleaningOrder->num_cleaners || $cleaningOrder->num_hours)
                        <div class="mb-3">
                            <p class="mb-2"><strong>Service Configuration:</strong></p>
                            <div class="row">
                                @if($cleaningOrder->num_cleaners)
                                    <div class="col-md-6 mb-2">
                                        <i class="bi bi-people-fill text-primary"></i>
                                        <strong>{{ $cleaningOrder->num_cleaners }}</strong> Cleaner{{ $cleaningOrder->num_cleaners > 1 ? 's' : '' }}
                                    </div>
                                @endif
                                @if($cleaningOrder->num_hours)
                                    <div class="col-md-6 mb-2">
                                        <i class="bi bi-clock-fill text-primary"></i>
                                        <strong>{{ $cleaningOrder->num_hours }}</strong> Hour{{ $cleaningOrder->num_hours > 1 ? 's' : '' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            @if($cleaningOrder->service_type)
                                <p><strong>Service Type:</strong><br>
                                    {{ ucfirst(str_replace('_', ' ', $cleaningOrder->service_type)) }}
                                </p>
                            @endif
                            @if($cleaningOrder->square_footage_range)
                                <p><strong>Square Footage:</strong><br>
                                    {{ $cleaningOrder->square_footage_range }}
                                </p>
                            @endif
                            <p><strong>Preferred Date & Time:</strong><br>
                                {{ $cleaningOrder->preferred_date->format('M d, Y') }} at {{ $cleaningOrder->preferred_time }}
                                @if($cleaningOrder->date_flexible || $cleaningOrder->time_flexible)
                                    <br><small class="text-muted">
                                        @if($cleaningOrder->date_flexible) Flexible with date (±2 days) @endif
                                        @if($cleaningOrder->time_flexible) Flexible with time (±2 hours) @endif
                                    </small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Parking:</strong><br>{{ $cleaningOrder->parking ?? 'N/A' }}</p>
                            <p><strong>Property Access:</strong><br>{{ $cleaningOrder->property_access ?? 'N/A' }}</p>
                            @if($cleaningOrder->access_notes)
                                <p><strong>Access Notes:</strong><br>{{ $cleaningOrder->access_notes }}</p>
                            @endif
                        </div>
                    </div>

                    @if($cleaningOrder->extras && count($cleaningOrder->extras) > 0)
                        <hr>
                        <p><strong>Extra Services:</strong></p>
                        <ul class="list-unstyled mb-0">
                            @foreach($cleaningOrder->extras as $extra)
                                <li class="mb-2">
                                    @if(isset($extra['id']))
                                        @php
                                            $serviceExtra = \App\Models\ServiceExtra::find($extra['id']);
                                        @endphp
                                        @if($serviceExtra)
                                            <i class="{{ $serviceExtra->icon_class }} text-primary"></i>
                                        @endif
                                    @endif
                                    {{ $extra['name'] ?? 'Extra Service' }}
                                    @if(isset($extra['price']))
                                        - <strong>${{ number_format($extra['price'], 2) }}</strong>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            @if($cleaningOrder->transaction)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Transaction Status:</strong><br>
                                <span class="badge bg-{{ $cleaningOrder->transaction->status === 'succeeded' ? 'success' : 'warning' }}">
                                    {{ $cleaningOrder->transaction->status_label }}
                                </span>
                            </p>
                            <p><strong>Payment Method:</strong><br>
                                {{ $cleaningOrder->transaction->payment_method_display }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            @if($cleaningOrder->transaction->stripe_session_id)
                                <p><strong>Stripe Session ID:</strong><br>
                                    <code>{{ $cleaningOrder->transaction->stripe_session_id }}</code>
                                </p>
                            @endif
                            @if($cleaningOrder->transaction->stripe_payment_intent_id)
                                <p><strong>Payment Intent ID:</strong><br>
                                    <code>{{ $cleaningOrder->transaction->stripe_payment_intent_id }}</code>
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($cleaningOrder->transaction->payment_succeeded_at)
                        <p class="mb-0"><strong>Payment Date:</strong><br>
                            {{ $cleaningOrder->transaction->payment_succeeded_at->format('M d, Y \a\t h:i A') }}
                        </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Admin Notes -->
            @if($cleaningOrder->admin_notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Notes</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $cleaningOrder->admin_notes }}</p>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column -->
        <div class="col-lg-4">

            <!-- Order Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Base Price:</span>
                        <strong>${{ number_format($cleaningOrder->base_price, 2) }}</strong>
                    </div>

                    @if($cleaningOrder->extras_total > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Extras:</span>
                            <strong>${{ number_format($cleaningOrder->extras_total, 2) }}</strong>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>${{ number_format($cleaningOrder->subtotal, 2) }}</strong>
                    </div>

                    @if($cleaningOrder->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>
                                Discount
                                @if($cleaningOrder->coupon_code)
                                    ({{ $cleaningOrder->coupon_code }})
                                @endif:
                            </span>
                            <strong>-${{ number_format($cleaningOrder->discount_amount, 2) }}</strong>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="h5">Total:</span>
                        <strong class="h5 text-primary">${{ number_format($cleaningOrder->total, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Status Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Management</h6>
                </div>
                <div class="card-body">
                    <p><strong>Current Status:</strong></p>
                    <div class="mb-3">
                        <span class="badge bg-{{ $cleaningOrder->status_color }} fs-6">
                            {{ $cleaningOrder->status_label }}
                        </span>
                    </div>

                    <form id="status-form">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Update Status:</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" {{ $cleaningOrder->status === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                                <option value="processing" {{ $cleaningOrder->status === 'processing' ? 'selected' : '' }}>Processing Payment</option>
                                <option value="paid" {{ $cleaningOrder->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="confirmed" {{ $cleaningOrder->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="scheduled" {{ $cleaningOrder->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ $cleaningOrder->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $cleaningOrder->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $cleaningOrder->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ $cleaningOrder->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes (Optional):</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3">{{ $cleaningOrder->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline</h6>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <strong>Order Created</strong><br>
                            <small class="text-muted">{{ $cleaningOrder->created_at->format('M d, Y h:i A') }}</small>
                        </li>

                        @if($cleaningOrder->paid_at)
                        <li class="timeline-item">
                            <strong>Payment Received</strong><br>
                            <small class="text-muted">{{ $cleaningOrder->paid_at->format('M d, Y h:i A') }}</small>
                        </li>
                        @endif

                        @if($cleaningOrder->confirmed_at)
                        <li class="timeline-item">
                            <strong>Order Confirmed</strong><br>
                            <small class="text-muted">{{ $cleaningOrder->confirmed_at->format('M d, Y h:i A') }}</small>
                        </li>
                        @endif

                        @if($cleaningOrder->completed_at)
                        <li class="timeline-item">
                            <strong>Service Completed</strong><br>
                            <small class="text-muted">{{ $cleaningOrder->completed_at->format('M d, Y h:i A') }}</small>
                        </li>
                        @endif

                        @if($cleaningOrder->cancelled_at)
                        <li class="timeline-item">
                            <strong>Order Cancelled</strong><br>
                            <small class="text-muted">{{ $cleaningOrder->cancelled_at->format('M d, Y h:i A') }}</small>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#status-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("admin.cleaning-orders.update-status", $cleaningOrder) }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Status updated successfully');
                location.reload();
            },
            error: function(xhr) {
                alert('Failed to update status');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.timeline {
    list-style: none;
    padding-left: 20px;
    border-left: 2px solid #e3e6f0;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -26px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #4e73df;
    border: 2px solid #fff;
}

.timeline-item:last-child {
    padding-bottom: 0;
}
</style>
@endpush
</x-app-layout>
