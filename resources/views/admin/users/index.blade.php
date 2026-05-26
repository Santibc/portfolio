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
        <div class="d-flex gap-2">
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-excel"
                href="{{ route('admin.usuarios.export-excel', request()->only(['search', 'role', 'sort', 'direction'])) }}">Excel</x-sinden.button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Usuario
            </button>
        </div>
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
                <div class="col-md-5 d-flex align-items-end">
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
                                <a href="{{ $sortLink('created_at') }}" class="text-decoration-none text-dark d-inline-flex align-items-center">
                                    Registro {!! $sortIcon('created_at') !!}
                                </a>
                            </th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios ?? [] as $usuario)
                        <tr class="{{ $usuario->activo ? '' : 'usuario-inactivo' }}">
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
                                        <h6 class="mb-0">
                                            {{ $usuario->name }}
                                            @if(!$usuario->activo)
                                                <span class="badge bg-danger ms-1">Inactivo</span>
                                            @endif
                                        </h6>
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
                                <small class="text-muted">{{ $usuario->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="editUser({{ $usuario->id }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($usuario->id !== auth()->id())
                                        @if($usuario->activo)
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="toggleActivoUser({{ $usuario->id }}, '{{ $usuario->name }}', false)" title="Desactivar">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="toggleActivoUser({{ $usuario->id }}, '{{ $usuario->name }}', true)" title="Activar">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
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

<!-- Form Activar/Desactivar -->
<form id="toggleActivoForm" method="POST" class="d-none">
    @csrf
    @method('PATCH')
</form>

@push('styles')
<style>
/* Filas de usuarios desactivados */
#usersTable tbody tr.usuario-inactivo > td {
    background-color: #f8d7da !important;
    color: #842029 !important;
}

#usersTable tbody tr.usuario-inactivo:hover > td {
    background-color: #f5c2c7 !important;
}

#usersTable tbody tr.usuario-inactivo .text-muted {
    color: #842029 !important;
    opacity: 0.85;
}

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

function toggleActivoUser(userId, userName, activar) {
    const titulo = activar ? '¿Activar usuario?' : '¿Desactivar usuario?';
    const texto = activar
        ? `El usuario "${userName}" podrá volver a iniciar sesión.`
        : `El usuario "${userName}" no podrá iniciar sesión. Su información e historial se mantienen intactos.`;
    const confirmar = activar ? 'Sí, activar' : 'Sí, desactivar';
    const color = activar ? '#198754' : '#dc3545';

    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmar,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('toggleActivoForm');
            form.action = `{{ url('admin/usuarios') }}/${userId}/toggle-activo`;
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
