<x-app-layout>
    <x-slot name="header">Gestión de Valores (Sobre Nosotros)</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Listado de Valores</h4>
            <a href="{{ route('admin.landing.valores.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Valor
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Icono</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($valores as $valor)
                            <tr>
                                <td>{{ $valor->orden }}</td>
                                <td><i class="{{ $valor->icono }} fs-4"></i></td>
                                <td><strong>{{ $valor->titulo }}</strong></td>
                                <td>{{ Str::limit($valor->descripcion, 60) }}</td>
                                <td>
                                    @if($valor->activo)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.landing.valores.edit', $valor->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.landing.valores.destroy', $valor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este valor?')">
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
                                <td colspan="6" class="text-center text-muted py-4">No hay valores registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $valores->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
