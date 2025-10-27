<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Australian Districts Management</h1>
                    <a href="{{ route('admin.districts.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Add New District
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
                            <table class="table table-hover" id="districtsTable">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Name</th>
                                        <th>State</th>
                                        <th>Postcode</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($districts as $district)
                                    <tr>
                                        <td>{{ $district->order }}</td>
                                        <td><strong>{{ $district->name }}</strong></td>
                                        <td>{{ $district->state }}</td>
                                        <td>{{ $district->postcode }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle"
                                                       type="checkbox"
                                                       role="switch"
                                                       id="status_{{ $district->id }}"
                                                       data-id="{{ $district->id }}"
                                                       {{ $district->is_active ? 'checked' : '' }}
                                                       style="cursor: pointer; width: 3rem; height: 1.5rem;">
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.districts.edit', $district) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-district" data-id="{{ $district->id }}">
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
            $('#districtsTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    search: "Search districts:",
                    lengthMenu: "Show _MENU_ districts per page"
                }
            });

            // Toggle status with AJAX
            $('.status-toggle').on('change', function() {
                const checkbox = $(this);
                const districtId = checkbox.data('id');
                const isActive = checkbox.is(':checked');

                $.ajax({
                    url: `/admin/districts/${districtId}/toggle-status`,
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
                            text: 'Failed to update district status. Please try again.',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            // Delete with SweetAlert confirmation
            $('.delete-district').on('click', function() {
                const districtId = $(this).data('id');
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
                            url: `/admin/districts/${districtId}`,
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
                                    text: 'District has been deleted.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete district. Please try again.',
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
