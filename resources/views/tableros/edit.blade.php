@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('tableros.show', $tablero) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h3 fw-bold mb-0" style="color: var(--manzer-primary);">Editar Tablero</h1>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('tableros.update', $tablero) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre del tablero <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $tablero->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descripcion</label>
                            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $tablero->descripcion) }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Color de fondo</label>
                                <div class="color-grid">
                                    @php
                                        $colores = ['#1e40af','#0891b2','#059669','#65a30d','#ca8a04','#ea580c','#dc2626','#be185d','#7c3aed','#475569','#ffffff'];
                                    @endphp
                                    @foreach($colores as $color)
                                    <div class="color-option {{ ($tablero->color_fondo ?? '#1e40af') === $color ? 'selected' : '' }}"
                                         style="background: {{ $color }};{{ $color === '#ffffff' ? ' border: 2px solid #ccc;' : '' }}"
                                         data-color="{{ $color }}"
                                         onclick="selectColor(this)"></div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="color_fondo" id="colorFondo" value="{{ old('color_fondo', $tablero->color_fondo) }}">

                                <label class="form-label fw-semibold mt-3">Imagen de fondo</label>
                                <input type="file" name="imagen_fondo" class="form-control form-control-sm @error('imagen_fondo') is-invalid @enderror"
                                       accept="image/*" id="imagenFondoInput">
                                @error('imagen_fondo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="imagenFondoPreview" class="mt-2" @if(!$tablero->imagen_fondo) style="display:none;" @endif>
                                    <img src="{{ $tablero->imagen_fondo ? asset('uploads/' . $tablero->imagen_fondo) : '' }}" alt="Preview"
                                         style="max-width:100%;max-height:120px;border-radius:6px;object-fit:cover;">
                                    @if($tablero->imagen_fondo)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="eliminarImagen" value="1">
                                        <label class="form-check-label small text-danger" for="eliminarImagen">Eliminar imagen</label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Visibilidad</label>
                                <select name="visibilidad" class="form-select" id="visibilidadSelect">
                                    <option value="miembros" {{ old('visibilidad', $tablero->visibilidad) === 'miembros' ? 'selected' : '' }}>Solo miembros</option>
                                    <option value="todos" {{ old('visibilidad', $tablero->visibilidad) === 'todos' ? 'selected' : '' }}>Todos los usuarios</option>
                                    <option value="roles" {{ old('visibilidad', $tablero->visibilidad) === 'roles' ? 'selected' : '' }}>Por roles</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="rolesContainer" style="{{ old('visibilidad', $tablero->visibilidad) === 'roles' ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold">Roles con acceso</label>
                            @foreach(['Administrador','Contabilidad','Encargado','RRHH','Auditor','Trabajador'] as $rol)
                            <div class="form-check">
                                <input class="form-check-input rol-checkbox" type="checkbox" name="roles_visibles[]"
                                       value="{{ $rol }}" id="rol_{{ $rol }}"
                                       {{ in_array($rol, old('roles_visibles', $tablero->roles_visibles ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="rol_{{ $rol }}">{{ $rol }}</label>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Vincular a obra (opcional)</label>
                            <select name="obra_id" class="form-select" id="obraSelect">
                                <option value="">Sin vincular</option>
                                @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" {{ old('obra_id', $tablero->obra_id) == $obra->id ? 'selected' : '' }}>
                                    {{ $obra->codigo }} - {{ $obra->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        {{-- Miembros section --}}
                        <h5 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Miembros del tablero</h5>
                        <p class="text-muted small mb-2">Al cambiar roles u obra se agregan automaticamente los usuarios correspondientes.</p>
                        <div class="mb-3">
                            <div class="input-group mb-3">
                                <select class="form-select" id="nuevoMiembroSelect">
                                    <option value="">Seleccionar usuario...</option>
                                    @foreach($usuarios as $usuario)
                                        @if(!$tablero->miembros->contains('id', $usuario->id))
                                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="btnAgregarMiembro">
                                    <i class="bi bi-plus-lg"></i> Agregar
                                </button>
                            </div>

                            <div id="listaMiembros">
                                @foreach($tablero->miembros as $miembro)
                                <div class="miembro-item d-flex align-items-center justify-content-between p-2 mb-2 bg-light rounded" data-user-id="{{ $miembro->id }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="miembro-avatar">{{ $miembro->initials }}</div>
                                        <div>
                                            <div class="fw-semibold small">{{ $miembro->name }}</div>
                                            <div class="text-muted" style="font-size:0.75rem;">{{ $miembro->pivot->rol }}</div>
                                        </div>
                                    </div>
                                    @if($miembro->id !== $tablero->creado_por)
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remover-miembro" data-user-id="{{ $miembro->id }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    @else
                                    <span class="badge bg-primary-subtle text-primary">Propietario</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tableros.show', $tablero) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectColor(el) {
    document.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('colorFondo').value = el.dataset.color;
}

document.addEventListener('DOMContentLoaded', function() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const tableroId = {{ $tablero->id }};
    const currentMemberIds = new Set(
        Array.from(document.querySelectorAll('#listaMiembros .miembro-item')).map(el => el.dataset.userId)
    );

    document.getElementById('visibilidadSelect').addEventListener('change', function() {
        document.getElementById('rolesContainer').style.display = this.value === 'roles' ? 'block' : 'none';
    });

    // Image preview
    document.getElementById('imagenFondoInput').addEventListener('change', function() {
        const preview = document.getElementById('imagenFondoPreview');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Add single member
    document.getElementById('btnAgregarMiembro').addEventListener('click', function() {
        const select = document.getElementById('nuevoMiembroSelect');
        const userId = select.value;
        if (!userId) return;
        agregarMiembro(userId);
    });

    function agregarMiembro(userId) {
        if (currentMemberIds.has(String(userId))) return Promise.resolve();

        return fetch(`/tableros/${tableroId}/miembros`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                currentMemberIds.add(String(userId));
                return true;
            }
            return false;
        });
    }

    // Remove member
    document.querySelectorAll('.btn-remover-miembro').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            Swal.fire({
                title: 'Eliminar miembro?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/tableros/${tableroId}/miembros/${userId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) location.reload();
                    });
                }
            });
        });
    });

    // Auto-add members by roles
    document.querySelectorAll('.rol-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const checkedRoles = Array.from(document.querySelectorAll('.rol-checkbox:checked')).map(cb => cb.value);
            if (checkedRoles.length === 0) return;

            const params = checkedRoles.map(r => 'roles[]=' + encodeURIComponent(r)).join('&');
            fetch('/tableros/usuarios-por-rol?' + params, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            })
            .then(r => r.json())
            .then(usuarios => {
                const promises = usuarios
                    .filter(u => !currentMemberIds.has(String(u.id)))
                    .map(u => agregarMiembro(u.id));
                Promise.all(promises).then(() => {
                    if (promises.length > 0) location.reload();
                });
            });
        });
    });

    // Auto-add members by obra
    document.getElementById('obraSelect').addEventListener('change', function() {
        const obraId = this.value;
        if (!obraId) return;

        fetch('/tableros/usuarios-por-obra/' + obraId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(usuarios => {
            const promises = usuarios
                .filter(u => !currentMemberIds.has(String(u.id)))
                .map(u => agregarMiembro(u.id));
            Promise.all(promises).then(() => {
                if (promises.length > 0) location.reload();
            });
        });
    });
});
</script>
@endsection

@push('styles')
<link href="{{ asset('css/tableros.css') }}" rel="stylesheet">
@endpush
