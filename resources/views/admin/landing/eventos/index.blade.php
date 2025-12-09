<x-app-layout>
    <x-slot name="header">Gestión de Eventos</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Listado de Eventos</h4>
            <a href="{{ route('admin.landing.eventos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Evento
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Imagen</th>
                                <th>Título</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($eventos as $evento)
                            <tr>
                                <td>{{ $evento->orden }}</td>
                                <td>
                                    @if($evento->imagen)
                                        <img src="{{ asset($evento->imagen) }}" style="max-height: 50px; max-width: 80px;" class="rounded">
                                    @else
                                        <span class="text-muted">Sin imagen</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $evento->titulo }}</strong>
                                    <br><small class="text-muted">{{ Str::limit($evento->extracto, 50) }}</small>
                                </td>
                                <td>{{ $evento->fecha ?? '-' }}</td>
                                <td>
                                    @if($evento->estado == 'proximo')
                                        <span class="badge bg-info">Próximo</span>
                                    @elseif($evento->estado == 'activo')
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Finalizado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($evento->activo)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.landing.eventos.edit', $evento->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.landing.eventos.destroy', $evento->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este evento?')">
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
                                <td colspan="7" class="text-center text-muted py-4">No hay eventos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $eventos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
