<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Blocked Service Zones</h1>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-plus-lg me-1"></i>Add Blocked Zone
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.blocked-zones.create', ['type' => 'polygon']) }}">
                        <i class="bi bi-bounding-box me-2"></i>Polygon (draw on map)
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.blocked-zones.create', ['type' => 'postcode']) }}">
                        <i class="bi bi-mailbox me-2"></i>Postcode
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.blocked-zones.create', ['type' => 'suburb']) }}">
                        <i class="bi bi-signpost-2 me-2"></i>Suburb / District name
                    </a></li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-1"></i>
            Customers attempting to book a service at any address that falls inside a blocked polygon, or that matches a blocked postcode/suburb, will be rejected at the location step of the calculator.
        </div>

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-polygons" type="button">
                    <i class="bi bi-bounding-box me-1"></i>Polygons <span class="badge bg-secondary ms-1">{{ $polygons->count() }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-postcodes" type="button">
                    <i class="bi bi-mailbox me-1"></i>Postcodes <span class="badge bg-secondary ms-1">{{ $postcodes->count() }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-suburbs" type="button">
                    <i class="bi bi-signpost-2 me-1"></i>Suburbs <span class="badge bg-secondary ms-1">{{ $suburbs->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-polygons">
                @include('admin.blocked-zones.partials._table', ['zones' => $polygons, 'columns' => ['Name', 'State', 'Points'], 'tableId' => 'polygonsTable'])
            </div>
            <div class="tab-pane fade" id="tab-postcodes">
                @include('admin.blocked-zones.partials._table', ['zones' => $postcodes, 'columns' => ['Name', 'Postcode', 'State'], 'tableId' => 'postcodesTable'])
            </div>
            <div class="tab-pane fade" id="tab-suburbs">
                @include('admin.blocked-zones.partials._table', ['zones' => $suburbs, 'columns' => ['Name', 'Suburb', 'State'], 'tableId' => 'suburbsTable'])
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            ['#polygonsTable', '#postcodesTable', '#suburbsTable'].forEach(id => {
                const $tbl = $(id);
                if ($tbl.length && $tbl.find('tbody tr[data-zone="1"]').length) {
                    $tbl.DataTable({ pageLength: 25, order: [[0, 'asc']] });
                }
            });

            $(document).on('change', '.status-toggle', function() {
                const checkbox = $(this);
                const id = checkbox.data('id');
                const isActive = checkbox.is(':checked');

                $.ajax({
                    url: `/admin/blocked-zones/${id}/toggle-status`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: (response) => {
                        Swal.fire({ icon: 'success', title: 'Updated', timer: 1200, showConfirmButton: false });
                    },
                    error: () => {
                        checkbox.prop('checked', !isActive);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Could not update status.' });
                    }
                });
            });

            $(document).on('click', '.delete-zone', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');

                Swal.fire({
                    title: 'Delete this blocked zone?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/blocked-zones/${id}`,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: () => {
                                row.fadeOut(300, function() { $(this).remove(); });
                                Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false });
                            },
                            error: () => Swal.fire({ icon: 'error', title: 'Error', text: 'Could not delete zone.' })
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
