@extends('layouts.app')

@section('title', $curso->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item active">{{ $curso->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Info del Curso -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    @if($curso->thumbnail)
                    <img src="{{ asset('storage/' . $curso->thumbnail) }}"
                         alt="{{ $curso->title }}"
                         class="w-100 rounded-3 mb-3"
                         style="height: 180px; object-fit: cover;">
                    @else
                    <div class="bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center"
                         style="height: 180px;">
                        <i class="bi bi-collection-play display-3 text-muted"></i>
                    </div>
                    @endif

                    <h4 class="mb-2">{{ $curso->title }}</h4>

                    <div class="d-flex gap-2 mb-3">
                        <span class="badge {{ $curso->is_published ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                            {{ $curso->is_published ? 'Publicado' : 'Borrador' }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary">
                            {{ $curso->category->name ?? 'Sin categoría' }}
                        </span>
                    </div>

                    <p class="text-muted">{{ $curso->description ?: 'Sin descripción' }}</p>

                    <hr>

                    <div class="row text-center">
                        <div class="col-3">
                            <h4 class="mb-1">{{ $curso->videos_count ?? 0 }}</h4>
                            <small class="text-muted">Videos</small>
                        </div>
                        <div class="col-3">
                            <h4 class="mb-1">{{ $curso->documents_count ?? $curso->documents->count() ?? 0 }}</h4>
                            <small class="text-muted">Docs</small>
                        </div>
                        <div class="col-3">
                            <h4 class="mb-1">{{ $curso->total_duration ?? '0:00' }}</h4>
                            <small class="text-muted">Duración</small>
                        </div>
                        <div class="col-3">
                            <h4 class="mb-1">{{ $curso->completions_count ?? 0 }}</h4>
                            <small class="text-muted">Completo</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="editCourse({{ $curso->id }})">
                            <i class="bi bi-pencil me-2"></i>Editar Curso
                        </button>
                        <a href="{{ route('admin.cursos.videos.index', $curso) }}" class="btn btn-primary">
                            <i class="bi bi-play-btn me-2"></i>Gestionar Videos
                        </a>
                        <a href="{{ route('admin.cursos.documents.index', $curso) }}" class="btn btn-info">
                            <i class="bi bi-file-earmark-text me-2"></i>Gestionar Documentos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2 text-primary"></i>
                        Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Tasa de completación</small>
                            <small>{{ $stats['completion_rate'] ?? 0 }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['completion_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">En progreso</small>
                            <small>{{ $stats['in_progress'] ?? 0 }} estudiantes</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['in_progress_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Sin iniciar</small>
                            <small>{{ $stats['not_started'] ?? 0 }} estudiantes</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $stats['not_started_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total notas</span>
                        <strong>{{ $curso->notes_count ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Videos -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-play-btn me-2 text-info"></i>
                            Videos del Curso
                        </h5>
                        <a href="{{ route('admin.cursos.videos.create', $curso) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Agregar Video
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($curso->videos && $curso->videos->count() > 0)
                    <div class="list-group list-group-flush" id="videosList">
                        @foreach($curso->videos as $index => $video)
                        <div class="list-group-item d-flex align-items-center py-3" data-video-id="{{ $video->id }}">
                            <span class="drag-handle me-3 text-muted" style="cursor: grab;">
                                <i class="bi bi-grip-vertical"></i>
                            </span>
                            <span class="badge bg-secondary me-3">{{ $index + 1 }}</span>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $video->title }}</h6>
                                <small class="text-muted">
                                    {{ $video->formatted_duration ?? '0:00' }} |
                                    {{ $video->completions_count ?? 0 }} visualizaciones
                                </small>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.cursos.videos.edit', [$curso, $video]) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="deleteVideo({{ $video->id }}, '{{ $video->title }}')" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-play-btn display-4 text-muted d-block mb-3"></i>
                        <h5>No hay videos</h5>
                        <p class="text-muted">Agrega videos a este curso</p>
                        <a href="{{ route('admin.cursos.videos.create', $curso) }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-2"></i>Agregar Video
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notas Recientes -->
            @if($curso->notes && $curso->notes->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-text me-2 text-warning"></i>
                        Notas Recientes
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($curso->notes->take(5) as $nota)
                        <div class="list-group-item">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 36px; height: 36px; flex-shrink: 0;">
                                    {{ strtoupper(substr($nota->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>{{ $nota->user->name ?? 'Usuario' }}</strong>
                                        <small class="text-muted">{{ $nota->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 text-muted">{{ Str::limit($nota->content, 150) }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Form eliminar video -->
<form id="deleteVideoForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Sortable para reordenar videos
const videosList = document.getElementById('videosList');
if (videosList) {
    new Sortable(videosList, {
        animation: 150,
        handle: '.drag-handle',
        onEnd: function(evt) {
            const order = [];
            videosList.querySelectorAll('[data-video-id]').forEach((el, index) => {
                order.push({
                    id: el.dataset.videoId,
                    order: index + 1
                });
            });

            fetch('{{ route("admin.cursos.videos.reorder", $curso) }}', {
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

function deleteVideo(videoId, videoTitle) {
    Swal.fire({
        title: '¿Eliminar video?',
        text: `¿Estás seguro de eliminar "${videoTitle}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteVideoForm');
            form.action = `{{ url('admin/cursos') }}/{{ $curso->id }}/videos/${videoId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
