<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="{{ $tableId }}">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zones as $zone)
                        <tr data-zone="1">
                            <td><strong>{{ $zone->name }}</strong></td>
                            @if($zone->type === 'polygon')
                                <td>{{ $zone->state ?? '—' }}</td>
                                <td><span class="badge bg-light text-dark">{{ is_array($zone->polygon_coordinates) ? count($zone->polygon_coordinates) : 0 }} pts</span></td>
                            @elseif($zone->type === 'postcode')
                                <td><code>{{ $zone->postcode }}</code></td>
                                <td>{{ $zone->state ?? '—' }}</td>
                            @elseif($zone->type === 'suburb')
                                <td>{{ $zone->suburb }}</td>
                                <td>{{ $zone->state ?? '—' }}</td>
                            @endif
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                           data-id="{{ $zone->id }}"
                                           {{ $zone->is_active ? 'checked' : '' }}
                                           style="cursor:pointer;width:3rem;height:1.5rem;">
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.blocked-zones.edit', $zone) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-zone" data-id="{{ $zone->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($columns) + 2 }}" class="text-center text-muted py-3">No blocked zones of this type yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
