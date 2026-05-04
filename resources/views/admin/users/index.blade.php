@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestión de Usuarios</h1>
            <p class="text-muted mb-0">Administra los usuarios de la plataforma</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-lg me-2"></i>Nuevo Usuario
        </button>
    </div>

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.usuarios.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre o email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select">
                        <option value="">Todos los roles</option>
                        @foreach($roles ?? [] as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Correo confirmado</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pendiente de correo</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                @php
                    $currentSort = $sort ?? 'name';
                    $currentDir = $direction ?? 'asc';
                    $sortLink = function ($column) use ($currentSort, $currentDir) {
                        $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
                        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $newDir]);
                    };
                    $sortIcon = function ($column) use ($currentSort, $currentDir) {
                        if ($currentSort !== $column) {
                            return '<i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: .75rem;"></i>';
                        }
                        return $currentDir === 'asc'
                            ? '<i class="bi bi-arrow-up ms-1 text-primary"></i>'
                            : '<i class="bi bi-arrow-down ms-1 text-primary"></i>';
                    };
                @endphp
                <table class="table table-hover align-middle mb-0" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">
                                <a href="{{ $sortLink('name') }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                                    Usuario {!! $sortIcon('name') !!}
                                </a>
                            </th>
                            <th>
                                <a href="{{ $sortLink('email') }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                                    Email {!! $sortIcon('email') !!}
                                </a>
                            </th>
                            <th>Rol</th>
                            <th>
                                <a href="{{ $sortLink('email_verified_at') }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                                    Estado {!! $sortIcon('email_verified_at') !!}
                                </a>
                                <button type="button"
                                        class="btn btn-link btn-sm p-0 ms-1 text-muted align-baseline"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="focus"
                                        data-bs-placement="top"
                                        data-bs-html="true"
                                        data-bs-title="¿Qué significa cada estado?"
                                        data-bs-content="<div class='mb-2'><span class='badge bg-success-subtle text-success'><i class='bi bi-check-circle me-1'></i>Correo confirmado</span><br><small class='text-muted'>El usuario verificó su correo electrónico y puede iniciar sesión normalmente.</small></div><div><span class='badge bg-warning-subtle text-warning'><i class='bi bi-clock me-1'></i>Pendiente de correo</span><br><small class='text-muted'>El usuario aún no ha confirmado su correo electrónico desde el enlace de verificación.</small></div>"
                                        title="Ver explicación de los estados">
                                    <i class="bi bi-question-circle"></i>
                                </button>
                            </th>
                            <th>
                                <a href="{{ $sortLink('created_at') }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                                    Registro {!! $sortIcon('created_at') !!}
                                </a>
                            </th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios ?? [] as $usuario)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if($usuario->hasProfilePhoto())
                                        <img src="{{ $usuario->profile_photo_url }}"
                                             alt="{{ $usuario->name }}"
                                             class="rounded-circle me-3"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            {{ $usuario->initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $usuario->name }}</h6>
                                        <small class="text-muted">ID: {{ $usuario->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @foreach($usuario->roles as $role)
                                <span class="badge {{ $role->name == 'Administrador' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}">
                                    {{ $role->name }}
                                </span>
                                @endforeach
                            </td>
                            <td>
                                @if($usuario->email_verified_at)
                                    <span class="badge bg-success-subtle text-success"
                                          data-bs-toggle="tooltip"
                                          title="El usuario verificó su correo y puede iniciar sesión.">
                                        <i class="bi bi-check-circle me-1"></i>Correo confirmado
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning"
                                          data-bs-toggle="tooltip"
                                          title="El usuario aún no ha confirmado su correo electrónico.">
                                        <i class="bi bi-clock me-1"></i>Pendiente de correo
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $usuario->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="editUser({{ $usuario->id }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($usuario->id !== auth()->id())
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteUser({{ $usuario->id }}, '{{ $usuario->name }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No hay usuarios que mostrar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @if(isset($usuarios) && is_object($usuarios) && method_exists($usuarios, 'hasPages') && $usuarios->hasPages())
        <div class="card-footer bg-transparent border-0">
            {{ $usuarios->links() }}
        </div>
    @endif
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.usuarios.store') }}" method="POST" id="createUserForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="createPassword" class="form-control" required minlength="8">
                            <button type="button" class="btn btn-outline-secondary" data-password-toggle="createPassword"><i class="bi bi-eye"></i></button>
                        </div>
                        <small class="text-muted">Mínimo 8 caracteres</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="createPasswordConfirmation" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" data-password-toggle="createPasswordConfirmation"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            @foreach($roles ?? [] as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
                        <div class="input-group">
                            <input type="password" name="password" id="editPassword" class="form-control" minlength="8" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" data-password-toggle="editPassword"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="editPasswordConfirmation" class="form-control" minlength="8" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" data-password-toggle="editPasswordConfirmation"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol <span class="text-danger">*</span></label>
                        <select name="role" id="editRole" class="form-select" required>
                            @foreach($roles ?? [] as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Eliminar -->
<form id="deleteUserForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('styles')
<style>
/* Fix para conflicto Bootstrap + Tailwind en modales */
.modal.show {
    display: block !important;
}

.modal-dialog {
    max-width: 500px !important;
    width: 500px !important;
    margin: 1.75rem auto !important;
    position: relative !important;
    transform: none !important;
    top: auto !important;
}

.modal.show .modal-dialog {
    transform: none !important;
}

.modal-content {
    width: 100% !important;
    position: relative !important;
}

.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5) !important;
}
</style>
@endpush

@push('scripts')
<script>
function editUser(userId) {
    fetch(`{{ url('admin/usuarios') }}/${userId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editName').value = data.name;
        document.getElementById('editEmail').value = data.email;
        document.getElementById('editRole').value = data.role;

        // Limpiar campos de contraseña
        document.querySelector('#editUserForm input[name="password"]').value = '';
        document.querySelector('#editUserForm input[name="password_confirmation"]').value = '';

        const formAction = `{{ url('admin/usuarios') }}/${userId}`;
        document.getElementById('editUserForm').action = formAction;

        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo cargar el usuario', 'error');
    });
}

function deleteUser(userId, userName) {
    Swal.fire({
        title: '¿Eliminar usuario?',
        text: `¿Estás seguro de eliminar a "${userName}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteUserForm');
            form.action = `{{ url('admin/usuarios') }}/${userId}`;
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.bootstrap) {
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => new bootstrap.Popover(el));
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    }
});

document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-password-toggle]');
    if (!btn) return;
    const input = document.getElementById(btn.dataset.passwordToggle);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});
</script>
@endpush
@endsection
