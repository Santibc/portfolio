<x-app-layout>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">Cleaning Orders</h2>
            <p class="text-muted">Manage all cleaning service orders</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-orders">{{ $stats['total_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="paid-orders">{{ $stats['paid_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pending-orders">{{ $stats['pending_orders'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-revenue">${{ number_format($stats['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
            <div>
                <button class="btn btn-sm btn-outline-secondary" id="refresh-btn">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="orders-table" width="100%">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.cleaning-orders.index") }}',
        columns: [
            { data: 'order_number', name: 'order_number' },
            { data: 'customer', name: 'first_name', orderable: false, searchable: true },
            { data: 'service', name: 'service_type', orderable: false },
            { data: 'date', name: 'preferred_date' },
            { data: 'amount', name: 'total' },
            { data: 'payment', name: 'payment', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[3, 'desc']],
        pageLength: 25,
        drawCallback: function() {
            updateStats();
        }
    });

    // Refresh button
    $('#refresh-btn').click(function() {
        table.ajax.reload();
    });

    // Update statistics
    function updateStats() {
        $.get('{{ route("admin.cleaning-orders.index") }}?stats=1', function(data) {
            // This would need a separate endpoint for stats
            // For now, we'll update after table loads
        });
    }

    // Delete order
    $(document).on('click', '.delete-order', function(e) {
        e.preventDefault();
        const url = $(this).data('url');

        if (confirm('Are you sure you want to delete this order?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    table.ajax.reload();
                    alert('Order deleted successfully');
                },
                error: function() {
                    alert('Failed to delete order');
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endpush
</x-app-layout>
