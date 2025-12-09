@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categorias.show', $course->category) }}">{{ $course->category->name ?? 'Categoría' }}</a></li>
            <li class="breadcrumb-item active">{{ $course->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Info del Curso -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                         alt="{{ $course->title }}"
                         class="w-100 rounded-3 mb-3"
                         style="height: 180px; object-fit: cover;">
                    @else
                    <div class="bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center"
                         style="height: 180px;">
                        <i class="bi bi-collection-play display-3 text-muted"></i>
                    </div>
                    @endif

                    <h3 class="mb-2">{{ $course->title }}</h3>

                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary">
                            {{ $course->category->name ?? 'Sin categoría' }}
                        </span>
                        <span class="badge bg-info-subtle text-info">
                            {{ $course->videos->count() }} videos
                        </span>
                    </div>

                    @if($course->description)
                    <p class="text-muted">{{ $course->description }}</p>
                    @endif

                    <!-- Progreso -->
                    @if($courseProgress)
                    <hr>
                    <div class="text-center mb-3">
                        <div class="position-relative d-inline-block">
                            <svg width="120" height="120" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="10"/>
                                <circle cx="60" cy="60" r="50" fill="none"
                                        stroke="{{ $courseProgress['is_completed'] ? '#198754' : '#0d6efd' }}"
                                        stroke-width="10"
                                        stroke-dasharray="{{ $courseProgress['percentage'] * 3.14 }} 314"
                                        stroke-linecap="round" transform="rotate(-90 60 60)"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h3 class="mb-0">{{ $courseProgress['percentage'] }}%</h3>
                            </div>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            {{ $courseProgress['completed_videos'] }} de {{ $courseProgress['total_videos'] }} videos
                        </p>
                    </div>

                    @if($courseProgress['is_completed'])
                    <div class="alert alert-success text-center mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        <strong>¡Curso completado!</strong>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            <!-- Notas del Curso -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-chat-left-text me-2 text-warning"></i>
                            Notas del Curso
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="notesList">
                        @forelse($notas ?? [] as $nota)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="small">{{ $nota->user->name ?? 'Usuario' }}</strong>
                                <small class="text-muted">{{ $nota->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 small text-muted">{{ Str::limit($nota->content, 100) }}</p>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="bi bi-chat-left d-block mb-2"></i>
                            Sin notas aún
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Videos -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-play-btn me-2 text-primary"></i>
                        Videos del Curso
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($course->videos as $index => $video)
                        <a href="{{ route('cursos.video', [$course, $video]) }}"
                           class="list-group-item list-group-item-action py-3 {{ $video->is_completed ? 'bg-success bg-opacity-10' : '' }}">
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $video->is_completed ? 'bg-success' : 'bg-secondary' }} me-3 rounded-circle"
                                      style="width: 32px; height: 32px; line-height: 24px;">
                                    @if($video->is_completed)
                                    <i class="bi bi-check"></i>
                                    @else
                                    {{ $index + 1 }}
                                    @endif
                                </span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 {{ $video->is_completed ? 'text-success' : '' }}">
                                        {{ $video->title }}
                                    </h6>
                                    @if($video->description)
                                    <p class="mb-0 small text-muted">{{ Str::limit($video->description, 80) }}</p>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $video->formatted_duration ?? '0:00' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Nota -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addNoteForm">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Agregar Nota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="content" class="form-control" rows="4"
                              placeholder="Escribe tu nota aquí..." required maxlength="1000"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('addNoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const content = this.querySelector('textarea[name="content"]').value;

    fetch('{{ route("notas.store", $course) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ content: content })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addNoteModal')).hide();
            this.reset();
            location.reload();
        } else {
            Swal.fire('Error', data.message || 'No se pudo guardar la nota', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Error al guardar la nota', 'error');
    });
});
</script>
@endpush
@endsection
