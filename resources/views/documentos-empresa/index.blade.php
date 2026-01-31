@extends('layouts.app')
@section('title', 'Documentos de Empresa')

@section('content')
<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Documentos de Empresa</h1>
            <p class="text-muted mb-0">Gestión centralizada de documentación corporativa</p>
        </div>
        <div>
            <a href="{{ route('documentos-empresa.create') }}" class="btn btn-primary">
                <i class="bi bi-upload me-2"></i>Subir Documento
            </a>
        </div>
    </div>

    {{-- ALERTAS DE SESIÓN --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-folder2 text-primary fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            <small class="text-muted">Total Documentos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['vigentes'] }}</h3>
                            <small class="text-muted">Vigentes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['proximos'] }}</h3>
                            <small class="text-muted">Próximos a Caducar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $stats['caducados'] }}</h3>
                            <small class="text-muted">Caducados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('documentos-empresa.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="buscar" class="form-control"
                               placeholder="Nombre o descripción..."
                               value="{{ request('buscar') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas las categorías</option>
                            @foreach(\App\Models\DocumentoEmpresa::CATEGORIAS as $key => $nombre)
                                <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="vigente" {{ request('estado') == 'vigente' ? 'selected' : '' }}>Vigentes</option>
                            <option value="proximo" {{ request('estado') == 'proximo' ? 'selected' : '' }}>Próximos a caducar</option>
                            <option value="caducado" {{ request('estado') == 'caducado' ? 'selected' : '' }}>Caducados</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('documentos-empresa.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA DE DOCUMENTOS --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Fecha Documento</th>
                        <th>Caducidad</th>
                        <th>Tamaño</th>
                        <th>Subido por</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $documento)
                    <tr>
                        <td>
                            @php
                                $iconoExtension = match(strtolower($documento->archivo_extension)) {
                                    'pdf' => 'bi bi-file-earmark-pdf text-danger',
                                    'doc', 'docx' => 'bi bi-file-earmark-word text-primary',
                                    'xls', 'xlsx' => 'bi bi-file-earmark-excel text-success',
                                    'jpg', 'jpeg', 'png' => 'bi bi-file-earmark-image text-info',
                                    default => 'bi bi-file-earmark text-secondary',
                                };
                            @endphp
                            <i class="{{ $iconoExtension }} fs-4"></i>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $documento->nombre }}</div>
                            @if($documento->descripcion)
                                <small class="text-muted">{{ Str::limit($documento->descripcion, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $documento->categoria_color }}">
                                <i class="{{ $documento->categoria_icono }} me-1"></i>
                                {{ $documento->categoria_nombre }}
                            </span>
                        </td>
                        <td>
                            @if($documento->fecha_documento)
                                {{ $documento->fecha_documento->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($documento->fecha_caducidad)
                                <div>{!! $documento->badge_caducidad !!}</div>
                                <small class="text-muted">{{ $documento->fecha_caducidad->format('d/m/Y') }}</small>
                            @else
                                <span class="badge bg-secondary">Sin caducidad</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $documento->archivo_tamaño_formateado }}</small>
                        </td>
                        <td>
                            <small>{{ $documento->subidoPor->name ?? 'Sistema' }}</small>
                            <br>
                            <small class="text-muted">{{ $documento->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('documentos-empresa.descargar', $documento) }}"
                               class="btn btn-sm btn-outline-success"
                               title="Descargar">
                                <i class="bi bi-download"></i>
                            </a>
                            <a href="{{ route('documentos-empresa.show', $documento) }}"
                               class="btn btn-sm btn-outline-info"
                               title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('documentos-empresa.edit', $documento) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="eliminarDocumento({{ $documento->id }}, '{{ addslashes($documento->nombre) }}')"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-folder2-open fs-1 d-block mb-3 text-muted"></i>
                            <p class="mb-0">No hay documentos registrados.</p>
                            <a href="{{ route('documentos-empresa.create') }}" class="btn btn-primary btn-sm mt-3">
                                <i class="bi bi-upload me-2"></i>Subir primer documento
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documentos->hasPages())
        <div class="card-footer bg-white">
            {{ $documentos->links() }}
        </div>
        @endif
    </div>

    {{-- DOCUMENTOS POR CATEGORÍA --}}
    @if(count($porCategoria) > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">Documentos por Categoría</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach(\App\Models\DocumentoEmpresa::CATEGORIAS as $key => $nombre)
                    @php
                        $cantidad = $porCategoria[$key] ?? 0;
                        $icono = \App\Models\DocumentoEmpresa::CATEGORIA_ICONOS[$key];
                        $color = \App\Models\DocumentoEmpresa::CATEGORIA_COLORES[$key];
                    @endphp
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('documentos-empresa.index', ['categoria' => $key]) }}"
                           class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border rounded hover-shadow">
                                <div class="bg-{{ $color }} bg-opacity-10 rounded-3 p-2 me-3">
                                    <i class="{{ $icono }} text-{{ $color }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $nombre }}</div>
                                    <small class="text-muted">{{ $cantidad }} documento{{ $cantidad != 1 ? 's' : '' }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.hover-shadow {
    transition: all 0.2s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script>
function eliminarDocumento(id, nombre) {
    Swal.fire({
        title: '¿Eliminar documento?',
        html: `Se eliminará: <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`{{ url('documentos-empresa') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'No se pudo eliminar el documento.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
            }
        }
    });
}
</script>
@endpush
