@extends('layouts.app')

@section('title', 'Documentos - ' . $curso->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.show', $curso) }}">{{ $curso->title }}</a></li>
            <li class="breadcrumb-item active">Documentos</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Documentos del Curso</h1>
            <p class="text-muted mb-0">{{ $curso->title }}</p>
        </div>
        <a href="{{ route('admin.cursos.documents.create', $curso) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Documento
        </a>
    </div>

    <!-- Lista de Documentos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($documents && $documents->count() > 0)
            <div class="list-group list-group-flush" id="documentsList">
                @foreach($documents as $index => $document)
                <div class="list-group-item py-3" data-document-id="{{ $document->id }}">
                    <div class="d-flex align-items-center">
                        <span class="drag-handle me-3 text-muted" style="cursor: grab;">
                            <i class="bi bi-grip-vertical fs-5"></i>
                        </span>
                        <span class="badge bg-secondary me-3 fs-6" style="min-width: 40px;">{{ $index + 1 }}</span>

                        <div class="me-3">
                            <i class="bi {{ $document->file_icon }} fs-2 {{ $document->file_icon_color }}"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $document->title }}</h5>
                                    @if($document->description)
                                    <p class="mb-0 text-muted small">{!! Str::limit(strip_tags($document->description), 100) !!}</p>
                                    @endif
                                    <div class="mt-2">
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="bi bi-file-earmark me-1"></i>
                                            {{ strtoupper($document->file_type) }}
                                        </span>
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="bi bi-hdd me-1"></i>
                                            {{ $document->formatted_file_size }}
                                        </span>
                                    </div>
                                </div>

                                <div class="btn-group ms-3">
                                    <a href="{{ route('admin.cursos.documents.download', [$curso, $document]) }}"
                                       class="btn btn-sm btn-outline-info" title="Descargar">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <a href="{{ route('admin.cursos.documents.edit', [$curso, $document]) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteDocument({{ $document->id }}, '{{ $document->title }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text display-3 text-muted d-block mb-3"></i>
                <h4>No hay documentos en este curso</h4>
                <p class="text-muted">Agrega el primer documento para comenzar</p>
                <a href="{{ route('admin.cursos.documents.create', $curso) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Agregar Documento
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Info adicional -->
    @if($documents && $documents->count() > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6">
                    <h4 class="text-primary mb-1">{{ $documents->count() }}</h4>
                    <small class="text-muted">Total Documentos</small>
                </div>
                <div class="col-md-6">
                    <h4 class="text-info mb-1">{{ formatBytes($documents->sum('file_size')) }}</h4>
                    <small class="text-muted">Tamaño Total</small>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Form eliminar documento -->
<form id="deleteDocumentForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Sortable para reordenar documentos
const documentsList = document.getElementById('documentsList');
if (documentsList) {
    new Sortable(documentsList, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function(evt) {
            const order = [];
            documentsList.querySelectorAll('[data-document-id]').forEach((el, index) => {
                order.push({
                    id: el.dataset.documentId,
                    order: index + 1
                });
            });

            fetch('{{ route("admin.cursos.documents.reorder", $curso) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            });
        }
    });
}

function deleteDocument(documentId, documentTitle) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: `¿Estás seguro de eliminar "${documentTitle}"? El archivo también será eliminado.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteDocumentForm');
            form.action = `{{ url('admin/cursos/' . $curso->id . '/documents') }}/${documentId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection

@php
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
@endphp
