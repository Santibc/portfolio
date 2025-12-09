<x-app-layout>
    <x-slot name="header">Gestión de Estadísticas (Sobre Nosotros)</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Listado de Estadísticas</h4>
            <a href="{{ route('admin.landing.estadisticas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Estadística
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Número</th>
                                <th>Etiqueta</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estadisticas as $estadistica)
                            <tr>
                                <td>{{ $estadistica->orden }}</td>
                                <td><strong class="fs-5 text-primary">{{ $estadistica->numero }}</strong></td>
                                <td>{{ $estadistica->etiqueta }}</td>
                                <td>
                                    @if($estadistica->activo)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.landing.estadisticas.edit', $estadistica->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.landing.estadisticas.destroy', $estadistica->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta estadística?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay estadísticas registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $estadisticas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
