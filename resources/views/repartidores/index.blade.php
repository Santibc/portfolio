<x-app-layout>
    <x-slot name="header">Repartidores</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-truck text-primary me-2"></i>Equipo de Repartidores
                        </h4>
                        <a href="{{ route('repartidores.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Nuevo Repartidor
                        </a>
                    </div>

                    @if($repartidores->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-truck display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay repartidores registrados</h5>
                            <p class="text-muted">Agrega repartidores para asignarlos a las entregas.</p>
                            <a href="{{ route('repartidores.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Agregar Primer Repartidor
                            </a>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($repartidores as $repartidor)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 {{ !$repartidor->activo ? 'border-secondary opacity-75' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-3">
                                                        {{ strtoupper(substr($repartidor->nombre, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h5 class="card-title mb-0">{{ $repartidor->nombre }}</h5>
                                                        @if($repartidor->vehiculo)
                                                            <small class="text-muted">
                                                                <i class="bi bi-{{ $repartidor->vehiculo == 'moto' ? 'scooter' : ($repartidor->vehiculo == 'bicicleta' ? 'bicycle' : 'car-front') }}"></i>
                                                                {{ ucfirst($repartidor->vehiculo) }}
                                                                @if($repartidor->placa)
                                                                    - {{ $repartidor->placa }}
                                                                @endif
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                           {{ $repartidor->activo ? 'checked' : '' }}
                                                           onchange="toggleActivo({{ $repartidor->id }}, this)">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <a href="tel:{{ $repartidor->telefono }}" class="text-decoration-none">
                                                    <i class="bi bi-telephone me-1"></i> {{ $repartidor->telefono }}
                                                </a>
                                            </div>

                                            @if($repartidor->documento)
                                                <div class="mb-3 text-muted small">
                                                    <i class="bi bi-person-vcard me-1"></i> {{ $repartidor->documento }}
                                                </div>
                                            @endif

                                            <div class="d-flex gap-3 mb-3">
                                                <div class="text-center">
                                                    <div class="h4 mb-0 text-warning">{{ $repartidor->entregas_pendientes }}</div>
                                                    <small class="text-muted">Pendientes</small>
                                                </div>
                                                <div class="text-center">
                                                    <div class="h4 mb-0 text-info">{{ $repartidor->entregas_hoy }}</div>
                                                    <small class="text-muted">Hoy</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group btn-group-sm w-100">
                                                <a href="{{ route('repartidores.edit', $repartidor) }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                                <form action="{{ route('repartidores.destroy', $repartidor) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('¿Eliminar este repartidor?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }
    </style>

    @push('scripts')
    <script>
        function toggleActivo(id, checkbox) {
            $.ajax({
                url: `/repartidores/${id}/toggle-activo`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        // Actualizar visualmente la card
                        const card = checkbox.closest('.card');
                        if (response.activo) {
                            card.classList.remove('border-secondary', 'opacity-75');
                        } else {
                            card.classList.add('border-secondary', 'opacity-75');
                        }
                    }
                },
                error: function() {
                    checkbox.checked = !checkbox.checked;
                    toastr.error('Error al cambiar el estado');
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
