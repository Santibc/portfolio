<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Coupons Management</h1>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Create New Coupon
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="couponsTable">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Usage</th>
                                        <th>Valid Until</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($coupons as $coupon)
                                    <tr>
                                        <td><strong>{{ $coupon->code }}</strong></td>
                                        <td>{{ $coupon->description ?? '-' }}</td>
                                        <td>
                                            @if($coupon->discount_type == 'percentage')
                                                <span class="badge bg-info">Percentage</span>
                                            @else
                                                <span class="badge bg-primary">Fixed Amount</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->discount_type == 'percentage')
                                                {{ $coupon->discount_value }}%
                                            @else
                                                ${{ $coupon->discount_value }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $coupon->usage_count }}
                                            @if($coupon->usage_limit)
                                                / {{ $coupon->usage_limit }}
                                            @else
                                                / ∞
                                            @endif
                                        </td>
                                        <td>{{ $coupon->expiry_date ? $coupon->expiry_date->format('M d, Y') : 'No expiry' }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle"
                                                       type="checkbox"
                                                       role="switch"
                                                       id="status_{{ $coupon->id }}"
                                                       data-id="{{ $coupon->id }}"
                                                       {{ $coupon->is_active ? 'checked' : '' }}
                                                       style="cursor: pointer; width: 3rem; height: 1.5rem;">
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-coupon" data-id="{{ $coupon->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#couponsTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    search: "Search coupons:",
                    lengthMenu: "Show _MENU_ coupons per page"
                }
            });

            // Toggle status with AJAX
            $('.status-toggle').on('change', function() {
                const checkbox = $(this);
                const couponId = checkbox.data('id');
                const isActive = checkbox.is(':checked');

                $.ajax({
                    url: `/admin/coupons/${couponId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        // Revert checkbox if error
                        checkbox.prop('checked', !isActive);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update coupon status. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            // Delete with SweetAlert confirmation
            $('.delete-coupon').on('click', function() {
                const couponId = $(this).data('id');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/coupons/${couponId}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                row.fadeOut(400, function() {
                                    $(this).remove();
                                });
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Coupon has been deleted.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete coupon. Please try again.',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
