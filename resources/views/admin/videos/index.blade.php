@extends('layouts.app')

@section('title', 'Videos - ' . $curso->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.index') }}">Cursos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cursos.show', $curso) }}">{{ $curso->title }}</a></li>
            <li class="breadcrumb-item active">Videos</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Videos del Curso</h1>
            <p class="text-muted mb-0">{{ $curso->title }}</p>
        </div>
        <a href="{{ route('admin.cursos.videos.create', $curso) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Video
        </a>
    </div>

    <!-- Lista de Videos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($videos && $videos->count() > 0)
            <div class="list-group list-group-flush" id="videosList">
                @foreach($videos as $index => $video)
                <div class="list-group-item py-3" data-video-id="{{ $video->id }}">
                    <div class="d-flex align-items-center">
                        <span class="drag-handle me-3 text-muted" style="cursor: grab;">
                            <i class="bi bi-grip-vertical fs-5"></i>
                        </span>
                        <span class="badge bg-primary me-3 fs-6" style="min-width: 40px;">{{ $index + 1 }}</span>

                        <div class="flex-grow-1">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $video->title }}</h5>
                                    <p class="mb-0 text-muted small">{{ Str::limit($video->description, 100) }}</p>
                                    <div class="mt-2">
                                        <span class="badge bg-info-subtle text-info me-2">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $video->formatted_duration ?? '0:00' }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            {{ $video->completions_count ?? 0 }} completados
                                        </span>
                                    </div>
                                </div>

                                <div class="btn-group ms-3">
                                    <a href="{{ asset('videos/' . $video->video_path) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-info" title="Ver video">
                                        <i class="bi bi-play-fill"></i>
                                    </a>
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
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-play-btn display-3 text-muted d-block mb-3"></i>
                <h4>No hay videos en este curso</h4>
                <p class="text-muted">Agrega el primer video para comenzar</p>
                <a href="{{ route('admin.cursos.videos.create', $curso) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Agregar Video
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Info adicional -->
    @if($videos && $videos->count() > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h4 class="text-primary mb-1">{{ $videos->count() }}</h4>
                    <small class="text-muted">Total Videos</small>
                </div>
                <div class="col-md-4">
                    <h4 class="text-info mb-1">{{ $totalDuration ?? '0:00' }}</h4>
                    <small class="text-muted">Duración Total</small>
                </div>
                <div class="col-md-4">
                    <h4 class="text-success mb-1">{{ $totalCompletions ?? 0 }}</h4>
                    <small class="text-muted">Videos Completados</small>
                </div>
            </div>
        </div>
    </div>
    @endif
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
        text: `¿Estás seguro de eliminar "${videoTitle}"? El archivo también será eliminado.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteVideoForm');
            form.action = `{{ url('admin/cursos/' . $curso->id . '/videos') }}/${videoId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
