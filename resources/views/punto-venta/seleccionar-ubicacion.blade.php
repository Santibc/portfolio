<x-app-layout>
    <x-slot name="header">Seleccionar Ubicación de Punto de Venta</x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Seleccione una Ubicación</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Seleccione la tienda o punto de venta donde desea trabajar.
                    </p>

                    @if($ubicaciones->count() > 0)
                        <form action="{{ route('punto-venta.cambiar-ubicacion') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                @foreach($ubicaciones as $ubicacion)
                                    <div class="col-md-6">
                                        <div class="card h-100 border-primary ubicacion-card" style="cursor: pointer;"
                                             onclick="seleccionarUbicacion({{ $ubicacion->id }})">
                                            <div class="card-body text-center">
                                                <i class="bi bi-shop fs-1 text-primary"></i>
                                                <h5 class="mt-2">{{ $ubicacion->nombre }}</h5>
                                                <p class="text-muted small mb-0">
                                                    <span class="badge bg-{{ $ubicacion->tipo === 'tienda' ? 'success' : 'info' }}">
                                                        {{ ucfirst($ubicacion->tipo) }}
                                                    </span>
                                                </p>
                                                @if($ubicacion->direccion)
                                                    <small class="text-muted">{{ $ubicacion->direccion }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="ubicacion_id" id="ubicacionSeleccionada">
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            No hay ubicaciones configuradas. Por favor, contacte al administrador.
                        </div>
                        <a href="{{ route('ubicaciones.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Crear Ubicación
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function seleccionarUbicacion(ubicacionId) {
            document.getElementById('ubicacionSeleccionada').value = ubicacionId;
            document.querySelector('form').submit();
        }

        // Efecto hover
        document.querySelectorAll('.ubicacion-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('shadow-lg');
            });
            card.addEventListener('mouseleave', function() {
                this.classList.remove('shadow-lg');
            });
        });
    </script>
    @endpush
</x-app-layout>
