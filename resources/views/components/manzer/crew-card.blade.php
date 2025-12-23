@props([
    'cuadrilla',
    'canEdit' => false,
    'canDelete' => false
])

<div class="crew-card h-100">
    <div class="crew-card-header">
        <div class="crew-icon">
            <i class="bi bi-people-fill fs-3"></i>
        </div>
        <div class="crew-status">
            @if($cuadrilla->activa)
                <x-manzer.badge variant="success">Activa</x-manzer.badge>
            @else
                <x-manzer.badge variant="secondary">Inactiva</x-manzer.badge>
            @endif
        </div>
    </div>

    <div class="crew-card-body">
        <h4 class="crew-name">{{ $cuadrilla->nombre }}</h4>

        {{-- Capataz --}}
        <div class="crew-leader mb-3">
            <small class="text-muted d-block mb-1">Capataz</small>
            @if($cuadrilla->capataz)
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-warning text-white me-2">
                        {{ strtoupper(substr($cuadrilla->capataz->nombre, 0, 1)) }}{{ strtoupper(substr($cuadrilla->capataz->apellidos, 0, 1)) }}
                    </div>
                    <span class="fw-semibold">{{ $cuadrilla->capataz->nombre_completo }}</span>
                </div>
            @else
                <span class="text-muted">Sin asignar</span>
            @endif
        </div>

        {{-- Descripción --}}
        @if($cuadrilla->descripcion)
        <div class="crew-description mb-3">
            <small class="text-muted d-block mb-1">Descripción</small>
            <span>{{ Str::limit($cuadrilla->descripcion, 80) }}</span>
        </div>
        @endif

        {{-- Miembros --}}
        <div class="crew-members">
            <small class="text-muted d-block mb-2">Miembros ({{ $cuadrilla->trabajadoresActivos->count() }})</small>
            <div class="avatar-group">
                @foreach($cuadrilla->trabajadoresActivos->take(5) as $trabajador)
                <div class="avatar avatar-sm bg-primary text-white" title="{{ $trabajador->nombre_completo }}">
                    {{ strtoupper(substr($trabajador->nombre, 0, 1)) }}{{ strtoupper(substr($trabajador->apellidos, 0, 1)) }}
                </div>
                @endforeach
                @if($cuadrilla->trabajadoresActivos->count() > 5)
                <div class="avatar avatar-sm bg-secondary text-white">
                    +{{ $cuadrilla->trabajadoresActivos->count() - 5 }}
                </div>
                @endif
                @if($cuadrilla->trabajadoresActivos->count() === 0)
                <span class="text-muted">Sin miembros</span>
                @endif
            </div>
        </div>
    </div>

    <div class="crew-card-footer">
        <a href="{{ route('cuadrillas.show', $cuadrilla) }}" class="btn btn-outline-info flex-grow-1">
            <i class="bi bi-eye me-2 fs-5"></i>Ver
        </a>
        @if($canEdit)
        <button type="button" class="btn btn-outline-primary px-3" onclick="editCuadrilla({{ $cuadrilla->id }})">
            <i class="bi bi-pencil fs-5"></i>
        </button>
        @endif
        @if($canDelete)
        <button type="button" class="btn btn-outline-danger px-3" onclick="deleteCuadrilla({{ $cuadrilla->id }}, '{{ $cuadrilla->nombre }}')">
            <i class="bi bi-trash fs-5"></i>
        </button>
        @endif
    </div>
</div>
