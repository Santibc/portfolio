@extends('layouts.app')

@section('title', 'Mis Notas')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1">Mis Notas</h1>
        <p class="text-muted mb-0">Todas las notas que has creado en tus cursos</p>
    </div>

    <div class="row g-4">
        @forelse($notas ?? [] as $nota)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $nota->course->title ?? 'Curso' }}</h6>
                            <small class="text-muted">{{ $nota->course->category->name ?? '' }}</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item" href="{{ route('cursos.show', $nota->course) }}">
                                        <i class="bi bi-eye me-2"></i>Ver curso
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item" onclick="editNote({{ $nota->id }}, `{{ addslashes($nota->content) }}`)">
                                        <i class="bi bi-pencil me-2"></i>Editar
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="deleteNote({{ $nota->id }})">
                                        <i class="bi bi-trash me-2"></i>Eliminar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <p class="mb-0">{{ $nota->content }}</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        {{ $nota->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-chat-left-text display-3 text-muted d-block mb-3"></i>
                    <h4>No tienes notas</h4>
                    <p class="text-muted">Las notas que crees en los cursos aparecerán aquí</p>
                    <a href="{{ route('cursos.index') }}" class="btn btn-primary">
                        Explorar Cursos
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if(isset($notas) && $notas->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $notas->links() }}
    </div>
    @endif
</div>

<!-- Modal Editar Nota -->
<div class="modal fade" id="editNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editNoteForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="note_id" id="editNoteId">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Editar Nota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="content" id="editNoteContent" class="form-control" rows="4"
                              required maxlength="1000"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Fix para dropdowns en cards */
.card {
    overflow: visible !important;
}
.card-header {
    overflow: visible !important;
}
.dropdown-menu {
    z-index: 1050 !important;
}
</style>
@endpush

@push('scripts')
<script>
function editNote(noteId, content) {
    document.getElementById('editNoteId').value = noteId;
    document.getElementById('editNoteContent').value = content;
    new bootstrap.Modal(document.getElementById('editNoteModal')).show();
}

document.getElementById('editNoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const noteId = document.getElementById('editNoteId').value;
    const content = document.getElementById('editNoteContent').value;

    fetch(`/notas/${noteId}`, {
        method: 'PUT',
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
            bootstrap.Modal.getInstance(document.getElementById('editNoteModal')).hide();
            location.reload();
        } else {
            Swal.fire('Error', data.message || 'No se pudo actualizar', 'error');
        }
    });
});

function deleteNote(noteId) {
    Swal.fire({
        title: '¿Eliminar nota?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
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
                    location.reload();
                }
            });
        }
    });
}
</script>
@endpush
@endsection
