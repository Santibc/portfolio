@extends('layouts.app')
@section('title', 'Documento: ' . $documento->nombre)

@section('content')
<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('documentos-empresa.index') }}">Documentos de Empresa</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($documento->nombre, 30) }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $documento->nombre }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('documentos-empresa.descargar', $documento) }}" class="btn btn-success">
                <i class="bi bi-download me-2"></i>Descargar
            </a>
            <a href="{{ route('documentos-empresa.edit', $documento) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Editar
            </a>
            <a href="{{ route('documentos-empresa.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- INFORMACIÓN PRINCIPAL --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Información del Documento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nombre</label>
                            <p class="mb-0 fw-semibold">{{ $documento->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Categoría</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $documento->categoria_color }}">
                                    <i class="{{ $documento->categoria_icono }} me-1"></i>
                                    {{ $documento->categoria_nombre }}
                                </span>
                            </p>
                        </div>
                        @if($documento->descripcion)
                        <div class="col-12">
                            <label class="form-label text-muted small">Descripción</label>
                            <p class="mb-0">{{ $documento->descripcion }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FECHAS --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>Fechas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha del Documento</label>
                            <p class="mb-0">
                                @if($documento->fecha_documento)
                                    {{ $documento->fecha_documento->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">No especificada</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Caducidad</label>
                            <p class="mb-0">
                                @if($documento->fecha_caducidad)
                                    {{ $documento->fecha_caducidad->format('d/m/Y') }}
                                    <br>
                                    {!! $documento->badge_caducidad !!}
                                @else
                                    <span class="badge bg-secondary">Sin caducidad</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Fecha de Subida</label>
                            <p class="mb-0">{{ $documento->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NOTAS --}}
            @if($documento->notas)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-sticky me-2"></i>Notas
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $documento->notas }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <div class="col-lg-4">
            {{-- ARCHIVO --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark me-2"></i>Archivo
                    </h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $iconoExtension = match(strtolower($documento->archivo_extension)) {
                            'pdf' => 'bi bi-file-earmark-pdf text-danger',
                            'doc', 'docx' => 'bi bi-file-earmark-word text-primary',
                            'xls', 'xlsx' => 'bi bi-file-earmark-excel text-success',
                            'jpg', 'jpeg', 'png' => 'bi bi-file-earmark-image text-info',
                            default => 'bi bi-file-earmark text-secondary',
                        };
                    @endphp

                    <i class="{{ $iconoExtension }}" style="font-size: 4rem;"></i>

                    <div class="mt-3">
                        <p class="mb-1 fw-semibold text-break">{{ $documento->archivo_nombre_original }}</p>
                        <p class="mb-0 text-muted small">
                            {{ strtoupper($documento->archivo_extension) }} &bull;
                            {{ $documento->archivo_tamaño_formateado }}
                        </p>
                    </div>

                    @if($documento->archivoExiste())
                        <div class="mt-3">
                            <a href="{{ route('documentos-empresa.descargar', $documento) }}"
                               class="btn btn-success w-100">
                                <i class="bi bi-download me-2"></i>Descargar Archivo
                            </a>
                        </div>
                        @if(in_array(strtolower($documento->archivo_extension), ['pdf', 'jpg', 'jpeg', 'png']))
                        <div class="mt-2">
                            <a href="{{ asset($documento->archivo_path) }}"
                               target="_blank"
                               class="btn btn-outline-primary w-100">
                                <i class="bi bi-eye me-2"></i>Ver en Nueva Pestaña
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            El archivo no existe en el servidor.
                        </div>
                    @endif
                </div>
            </div>

            {{-- METADATOS --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>Información de Registro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subido por:</span>
                        <span class="fw-semibold">{{ $documento->subidoPor->name ?? 'Sistema' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Fecha de subida:</span>
                        <span>{{ $documento->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($documento->updated_at->ne($documento->created_at))
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Última modificación:</span>
                        <span>{{ $documento->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ACCIONES RÁPIDAS --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('documentos-empresa.edit', $documento) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Editar Documento
                        </a>
                        <button type="button"
                                class="btn btn-outline-danger"
                                onclick="eliminarDocumento({{ $documento->id }}, '{{ addslashes($documento->nombre) }}')">
                            <i class="bi bi-trash me-2"></i>Eliminar Documento
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
                        window.location.href = '{{ route('documentos-empresa.index') }}';
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
