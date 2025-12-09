@extends('layouts.app')

@section('title', $video->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cursos.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active">{{ $video->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Video Player -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <!-- Player de Video -->
                    <div class="video-player-container bg-dark rounded-top">
                        <video id="videoPlayer" class="w-100" controls
                               style="max-height: 500px;"
                               poster="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : '' }}">
                            <source src="{{ asset('videos/' . $video->video_path) }}" type="video/mp4">
                            Tu navegador no soporta el elemento de video.
                        </video>
                    </div>

                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="mb-1">{{ $video->title }}</h3>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-collection-play me-1"></i>
                                    {{ $course->title }}
                                </p>
                            </div>
                            <div>
                                <button type="button" id="markCompleteBtn"
                                        class="btn {{ $videoCompleted ? 'btn-success' : 'btn-outline-success' }}"
                                        {{ $videoCompleted ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle me-2"></i>
                                    {{ $videoCompleted ? 'Completado' : 'Marcar como visto' }}
                                </button>
                            </div>
                        </div>

                        @if($video->description)
                        <div class="border-top pt-3">
                            <h6>Descripción</h6>
                            <p class="text-muted mb-0">{{ $video->description }}</p>
                        </div>
                        @endif

                        <!-- Navegación entre videos -->
                        <div class="border-top pt-3 mt-3">
                            <div class="row">
                                <div class="col-6">
                                    @if($previousVideo)
                                    <a href="{{ route('cursos.video', [$course, $previousVideo]) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-chevron-left me-2"></i>
                                        Anterior
                                    </a>
                                    @endif
                                </div>
                                <div class="col-6 text-end">
                                    @if($nextVideo)
                                    <a href="{{ route('cursos.video', [$course, $nextVideo]) }}" class="btn btn-primary" id="nextVideoBtn">
                                        Siguiente
                                        <i class="bi bi-chevron-right ms-2"></i>
                                    </a>
                                    @else
                                    <a href="{{ route('cursos.show', $course) }}" class="btn btn-success">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Finalizar Curso
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progreso del Curso -->
            @if($courseProgress)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Progreso del Curso</h6>
                        <span class="badge bg-primary">{{ $courseProgress['percentage'] }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" id="courseProgressBar"
                             style="width: {{ $courseProgress['percentage'] }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ $courseProgress['completed_videos'] }} de {{ $courseProgress['total_videos'] }} videos completados
                    </small>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Lista de Videos -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-list-ol me-2 text-primary"></i>
                        Contenido del Curso
                    </h6>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @foreach($course->videos as $index => $v)
                        <a href="{{ route('cursos.video', [$course, $v]) }}"
                           class="list-group-item list-group-item-action py-2 {{ $v->id === $video->id ? 'active' : '' }} {{ $v->is_completed ? 'bg-success bg-opacity-10' : '' }}">
                            <div class="d-flex align-items-center">
                                <span class="me-2">
                                    @if($v->is_completed)
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    @elseif($v->id === $video->id)
                                    <i class="bi bi-play-circle-fill"></i>
                                    @else
                                    <span class="badge bg-secondary rounded-circle" style="width: 20px; height: 20px; font-size: 10px; line-height: 14px;">
                                        {{ $index + 1 }}
                                    </span>
                                    @endif
                                </span>
                                <span class="flex-grow-1 small {{ $v->id === $video->id ? 'fw-bold' : '' }}">
                                    {{ Str::limit($v->title, 30) }}
                                </span>
                                <small class="text-muted">{{ $v->formatted_duration ?? '' }}</small>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Agregar Nota -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-pencil-square me-2 text-warning"></i>
                        Agregar Nota
                    </h6>
                </div>
                <div class="card-body">
                    <form id="quickNoteForm">
                        @csrf
                        <textarea name="content" class="form-control mb-3" rows="3"
                                  placeholder="Escribe una nota sobre este video..." maxlength="1000"></textarea>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-2"></i>Guardar Nota
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const videoPlayer = document.getElementById('videoPlayer');
const markCompleteBtn = document.getElementById('markCompleteBtn');
const isCompleted = {{ $videoCompleted ? 'true' : 'false' }};

// Marcar como completado al terminar el video
videoPlayer.addEventListener('ended', function() {
    if (!isCompleted) {
        markAsComplete();
    }
});

// Marcar manualmente como completado
markCompleteBtn.addEventListener('click', function() {
    if (!isCompleted) {
        markAsComplete();
    }
});

function markAsComplete() {
    fetch('{{ route("progreso.marcar", $video) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            markCompleteBtn.classList.remove('btn-outline-success');
            markCompleteBtn.classList.add('btn-success');
            markCompleteBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Completado';
            markCompleteBtn.disabled = true;

            // Actualizar barra de progreso
            if (data.data && data.data.course_progress) {
                const progressBar = document.getElementById('courseProgressBar');
                if (progressBar) {
                    progressBar.style.width = data.data.course_progress.percentage + '%';
                }
            }

            // Mostrar notificación
            Swal.fire({
                icon: 'success',
                title: '¡Video completado!',
                text: data.data.course_completed ? '¡Felicidades! Has completado el curso.' : 'Continúa con el siguiente video.',
                timer: 2000,
                showConfirmButton: false
            });

            // Si completó el curso, mostrar mensaje especial
            if (data.data.course_completed) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: '🎉 ¡Curso Completado!',
                        html: data.data.next_course ?
                            `Has desbloqueado: <strong>${data.data.next_course.title}</strong>` :
                            '¡Felicidades por completar este curso!',
                        confirmButtonText: data.data.next_course ? 'Ir al siguiente curso' : 'Volver al curso'
                    }).then((result) => {
                        if (result.isConfirmed && data.data.next_course) {
                            window.location.href = `/cursos/${data.data.next_course.slug}`;
                        }
                    });
                }, 2500);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Guardar nota rápida
document.getElementById('quickNoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const content = this.querySelector('textarea[name="content"]').value;

    if (!content.trim()) {
        Swal.fire('Error', 'Escribe algo en la nota', 'warning');
        return;
    }

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
            this.reset();
            Swal.fire({
                icon: 'success',
                title: 'Nota guardada',
                timer: 1500,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        Swal.fire('Error', 'No se pudo guardar la nota', 'error');
    });
});
</script>
@endpush

@push('styles')
<style>
.video-player-container {
    position: relative;
    background: #000;
}
.video-player-container video {
    display: block;
}
</style>
@endpush
@endsection
