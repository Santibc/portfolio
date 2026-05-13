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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

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
                            @if($cleaningOrder->latitude && $cleaningOrder->longitude)
                                <p class="mb-0">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $cleaningOrder->latitude }},{{ $cleaningOrder->longitude }}"
                                       target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-geo-alt-fill"></i> Open in Google Maps
                                    </a>
                                </p>
                            @endif
                            @if($cleaningOrder->suburb || $cleaningOrder->state || $cleaningOrder->postcode)
                                <p class="text-muted small mt-2 mb-0">
                                    Suburb: <strong>{{ $cleaningOrder->suburb ?? '—' }}</strong> |
                                    State: <strong>{{ $cleaningOrder->state ?? '—' }}</strong> |
                                    Postcode: <strong>{{ $cleaningOrder->postcode ?? '—' }}</strong>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual Payment / Comprobante -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-receipt"></i> Manual Payment / Receipt</h6>
                    @if($cleaningOrder->payment_proof_path)
                        <span class="badge bg-success">Receipt uploaded</span>
                    @else
                        <span class="badge bg-warning">No receipt yet</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($cleaningOrder->payment_proof_path)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $cleaningOrder->payment_method_manual ?? '—')) }}</p>
                                <p class="mb-1"><strong>Reference:</strong> {{ $cleaningOrder->payment_reference ?? '—' }}</p>
                                <p class="mb-1"><strong>Uploaded:</strong>
                                    @if($cleaningOrder->payment_proof_uploaded_at)
                                        {{ $cleaningOrder->payment_proof_uploaded_at->format('M d, Y h:i A') }}
                                    @endif
                                </p>
                                <a href="{{ asset($cleaningOrder->payment_proof_path) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                    <i class="bi bi-eye"></i> View receipt
                                </a>
                                <form method="POST" action="{{ route('admin.cleaning-orders.payment-proof.delete', $cleaningOrder) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger mt-2"
                                            onclick="return confirm('Remove this receipt?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 text-center">
                                @php
                                    $ext = strtolower(pathinfo($cleaningOrder->payment_proof_path, PATHINFO_EXTENSION));
                                @endphp
                                @if(in_array($ext, ['jpg','jpeg','png']))
                                    <img src="{{ asset($cleaningOrder->payment_proof_path) }}" alt="Receipt"
                                         style="max-width:100%; max-height:280px; border:1px solid #dee2e6; border-radius:6px;">
                                @else
                                    <div class="border rounded p-4 bg-light">
                                        <i class="bi bi-file-pdf text-danger" style="font-size:4rem;"></i>
                                        <div class="text-muted small">PDF file</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <p class="small text-muted mb-2">Replace the receipt by uploading a new one below.</p>
                    @endif

                    <form method="POST" action="{{ route('admin.cleaning-orders.payment-proof.upload', $cleaningOrder) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Method</label>
                                <select name="payment_method_manual" class="form-select">
                                    <option value="bank_transfer" @selected($cleaningOrder->payment_method_manual==='bank_transfer')>Bank transfer</option>
                                    <option value="cash" @selected($cleaningOrder->payment_method_manual==='cash')>Cash</option>
                                    <option value="card_terminal" @selected($cleaningOrder->payment_method_manual==='card_terminal')>Card terminal</option>
                                    <option value="other" @selected($cleaningOrder->payment_method_manual==='other')>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference</label>
                                <input type="text" name="payment_reference" class="form-control" maxlength="255"
                                       value="{{ $cleaningOrder->payment_reference }}" placeholder="Tx ID or note">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">File (jpg/png/pdf, ≤5MB)</label>
                                <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-cloud-upload"></i> Upload Receipt
                                </button>
                            </div>
                        </div>
                    </form>
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
