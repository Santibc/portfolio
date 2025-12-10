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
                            <source src="{{ asset($video->video_path) }}" type="video/mp4">
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

            <!-- Sección de Notas (estilo Platzi) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-sticky me-2 text-warning"></i>
                        Mis Notas
                        <span class="badge bg-secondary ms-2" id="notesCount">0</span>
                    </h6>
                    <button type="button" class="btn btn-primary btn-sm" id="addNoteBtn">
                        <i class="bi bi-plus-lg me-1"></i>
                        Agregar Nota
                    </button>
                </div>
                <div class="card-body">
                    <!-- Formulario para agregar nota -->
                    <div id="noteFormContainer" class="mb-4" style="display: none;">
                        <form id="noteForm">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <span class="badge bg-primary" id="currentTimestamp">0:00</span>
                                <span class="text-muted small">Momento actual del video</span>
                            </div>
                            <textarea id="noteContent" class="form-control mb-2" rows="3"
                                      placeholder="Escribe tu nota aquí..." maxlength="5000"></textarea>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="cancelNoteBtn">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-save me-1"></i>Guardar
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de notas -->
                    <div id="notesList">
                        <div class="text-center text-muted py-4" id="noNotesMessage">
                            <i class="bi bi-sticky display-4 d-block mb-2 opacity-50"></i>
                            <p class="mb-0">No tienes notas en este video</p>
                            <small>Haz clic en "Agregar Nota" para crear una</small>
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-list-ol me-2 text-primary"></i>
                        Contenido del Curso
                    </h6>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
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
        </div>
    </div>
</div>

@push('scripts')
<script>
const videoPlayer = document.getElementById('videoPlayer');
const markCompleteBtn = document.getElementById('markCompleteBtn');
const isCompleted = {{ $videoCompleted ? 'true' : 'false' }};
const videoId = {{ $video->id }};

// Variables para notas
let currentNotes = [];

// =====================================================
// FUNCIONES DE VIDEO
// =====================================================

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

            Swal.fire({
                icon: 'success',
                title: '¡Video completado!',
                text: data.data.course_completed ? '¡Felicidades! Has completado el curso.' : 'Continúa con el siguiente video.',
                timer: 2000,
                showConfirmButton: false
            });

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

// =====================================================
// FUNCIONES DE NOTAS
// =====================================================

function formatTime(seconds) {
    if (!seconds && seconds !== 0) return null;
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function loadNotes() {
    fetch(`/videos/${videoId}/notas`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentNotes = data.data;
            renderNotes();
        }
    })
    .catch(error => console.error('Error loading notes:', error));
}

function renderNotes() {
    const notesList = document.getElementById('notesList');
    const noNotesMessage = document.getElementById('noNotesMessage');
    const notesCount = document.getElementById('notesCount');

    // Filtrar solo notas del usuario actual
    const myNotes = currentNotes.filter(note => note.user_id === {{ auth()->id() }});
    notesCount.textContent = myNotes.length;

    if (myNotes.length === 0) {
        notesList.innerHTML = `
            <div class="text-center text-muted py-4" id="noNotesMessage">
                <i class="bi bi-sticky display-4 d-block mb-2 opacity-50"></i>
                <p class="mb-0">No tienes notas en este video</p>
                <small>Haz clic en "Agregar Nota" para crear una</small>
            </div>
        `;
        return;
    }

    notesList.innerHTML = myNotes.map(note => `
        <div class="note-item border rounded p-3 mb-2" data-note-id="${note.id}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    ${note.timestamp_seconds !== null ? `
                        <button type="button" class="btn btn-link btn-sm p-0 text-primary seek-to-time" data-time="${note.timestamp_seconds}">
                            <i class="bi bi-play-fill me-1"></i>${formatTime(note.timestamp_seconds)}
                        </button>
                    ` : `
                        <span class="badge bg-secondary">Sin timestamp</span>
                    `}
                </div>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary btn-sm edit-note-btn" data-note-id="${note.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete-note-btn" data-note-id="${note.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <p class="mb-1 note-content">${escapeHtml(note.content)}</p>
            <small class="text-muted">${new Date(note.created_at).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })}</small>
        </div>
    `).join('');

    // Agregar event listeners
    document.querySelectorAll('.seek-to-time').forEach(btn => {
        btn.addEventListener('click', function() {
            const time = parseInt(this.dataset.time);
            videoPlayer.currentTime = time;
            videoPlayer.play();
        });
    });

    document.querySelectorAll('.edit-note-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            editNote(parseInt(this.dataset.noteId));
        });
    });

    document.querySelectorAll('.delete-note-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteNote(parseInt(this.dataset.noteId));
        });
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function saveNote(content, timestampSeconds, noteId = null) {
    const url = noteId ? `/notas/${noteId}` : `/videos/${videoId}/notas`;
    const method = noteId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            content: content,
            timestamp_seconds: timestampSeconds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotes();
            hideNoteForm();
            Swal.fire({
                icon: 'success',
                title: noteId ? 'Nota actualizada' : 'Nota guardada',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire('Error', data.message || 'No se pudo guardar la nota', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'No se pudo guardar la nota', 'error');
    });
}

function editNote(noteId) {
    const note = currentNotes.find(n => n.id === noteId);
    if (!note) return;

    document.getElementById('noteContent').value = note.content;
    document.getElementById('noteFormContainer').style.display = 'block';
    document.getElementById('noteFormContainer').dataset.editId = noteId;

    if (note.timestamp_seconds !== null) {
        document.getElementById('currentTimestamp').textContent = formatTime(note.timestamp_seconds);
    }

    document.getElementById('noteContent').focus();
}

function deleteNote(noteId) {
    Swal.fire({
        title: '¿Eliminar nota?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/notas/${noteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotes();
                    Swal.fire({
                        icon: 'success',
                        title: 'Nota eliminada',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}

function showNoteForm() {
    document.getElementById('noteFormContainer').style.display = 'block';
    document.getElementById('noteFormContainer').dataset.editId = '';
    document.getElementById('noteContent').value = '';
    updateCurrentTimestamp();
    document.getElementById('noteContent').focus();
}

function hideNoteForm() {
    document.getElementById('noteFormContainer').style.display = 'none';
    document.getElementById('noteFormContainer').dataset.editId = '';
    document.getElementById('noteContent').value = '';
}

function updateCurrentTimestamp() {
    const time = Math.floor(videoPlayer.currentTime);
    document.getElementById('currentTimestamp').textContent = formatTime(time);
}

// Event listeners para notas
document.getElementById('addNoteBtn').addEventListener('click', showNoteForm);
document.getElementById('cancelNoteBtn').addEventListener('click', hideNoteForm);

document.getElementById('noteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const content = document.getElementById('noteContent').value.trim();
    if (!content) {
        Swal.fire('Error', 'Escribe algo en la nota', 'warning');
        return;
    }

    const editId = document.getElementById('noteFormContainer').dataset.editId;
    const timestamp = Math.floor(videoPlayer.currentTime);

    saveNote(content, timestamp, editId ? parseInt(editId) : null);
});

// Actualizar timestamp mientras el video corre
videoPlayer.addEventListener('timeupdate', function() {
    if (document.getElementById('noteFormContainer').style.display !== 'none' &&
        !document.getElementById('noteFormContainer').dataset.editId) {
        updateCurrentTimestamp();
    }
});

// Cargar notas al iniciar
document.addEventListener('DOMContentLoaded', loadNotes);
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
.note-item {
    transition: all 0.2s;
}
.note-item:hover {
    background-color: #f8f9fa;
}
.seek-to-time {
    text-decoration: none;
}
.seek-to-time:hover {
    text-decoration: underline;
}
</style>
@endpush
@endsection
