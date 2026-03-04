@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('tableros.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h1 class="h3 fw-bold mb-0" style="color: var(--manzer-primary);">Nuevo Tablero</h1>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('tableros.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre del tablero <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" required autofocus placeholder="Ej: Proyecto Norte, Tareas Semanales...">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descripcion</label>
                            <textarea name="descripcion" class="form-control" rows="3"
                                      placeholder="Describe el proposito de este tablero...">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Color de fondo</label>
                                <div class="color-grid">
                                    @php
                                        $colores = ['#1e40af','#0891b2','#059669','#65a30d','#ca8a04','#ea580c','#dc2626','#be185d','#7c3aed','#475569','#ffffff'];
                                    @endphp
                                    @foreach($colores as $color)
                                    <div class="color-option {{ $color === '#1e40af' ? 'selected' : '' }}"
                                         style="background: {{ $color }};{{ $color === '#ffffff' ? ' border: 2px solid #ccc;' : '' }}"
                                         data-color="{{ $color }}"
                                         onclick="selectColor(this)"></div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="color_fondo" id="colorFondo" value="{{ old('color_fondo', '#1e40af') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Visibilidad</label>
                                <select name="visibilidad" class="form-select" id="visibilidadSelect">
                                    <option value="miembros" {{ old('visibilidad', 'miembros') === 'miembros' ? 'selected' : '' }}>
                                        Solo miembros
                                    </option>
                                    <option value="todos" {{ old('visibilidad') === 'todos' ? 'selected' : '' }}>
                                        Todos los usuarios
                                    </option>
                                    <option value="roles" {{ old('visibilidad') === 'roles' ? 'selected' : '' }}>
                                        Por roles
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="rolesContainer" style="display: none;">
                            <label class="form-label fw-semibold">Roles con acceso</label>
                            @foreach(['Administrador','Contabilidad','Encargado','RRHH','Auditor','Trabajador'] as $rol)
                            <div class="form-check">
                                <input class="form-check-input rol-checkbox" type="checkbox" name="roles_visibles[]"
                                       value="{{ $rol }}" id="rol_{{ $rol }}"
                                       {{ in_array($rol, old('roles_visibles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="rol_{{ $rol }}">{{ $rol }}</label>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Vincular a obra (opcional)</label>
                            <select name="obra_id" class="form-select" id="obraSelect">
                                <option value="">Sin vincular</option>
                                @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" {{ old('obra_id') == $obra->id ? 'selected' : '' }}>
                                    {{ $obra->codigo }} - {{ $obra->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <h5 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Miembros del tablero</h5>
                        <p class="text-muted small mb-2">Se seleccionan automaticamente al elegir roles u obra. Puedes agregar o quitar manualmente.</p>
                        <div class="mb-4">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control form-control-sm" id="buscarMiembro"
                                       placeholder="Buscar usuario...">
                            </div>
                            <div id="miembrosContainer" style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
                                @foreach($usuarios as $usuario)
                                <div class="form-check miembro-check" data-user-id="{{ $usuario->id }}" data-name="{{ strtolower($usuario->name) }}">
                                    <input class="form-check-input miembro-checkbox" type="checkbox" name="miembros[]"
                                           value="{{ $usuario->id }}" id="miembro_{{ $usuario->id }}"
                                           {{ in_array($usuario->id, old('miembros', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="miembro_{{ $usuario->id }}">{{ $usuario->name }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-1 small text-muted">
                                <span id="miembrosCount">0</span> miembros seleccionados
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tableros.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Crear Tablero
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

function updateMiembrosCount() {
    const count = document.querySelectorAll('.miembro-checkbox:checked').length;
    document.getElementById('miembrosCount').textContent = count;
}

document.addEventListener('DOMContentLoaded', function() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const visibilidadSelect = document.getElementById('visibilidadSelect');
    const rolesContainer = document.getElementById('rolesContainer');
    const obraSelect = document.getElementById('obraSelect');
    const buscarMiembro = document.getElementById('buscarMiembro');

    // Show/hide roles container
    visibilidadSelect.addEventListener('change', function() {
        rolesContainer.style.display = this.value === 'roles' ? 'block' : 'none';
        if (this.value !== 'roles') {
            // Clear role auto-selections when switching away from roles
            autoSelectByRoles();
        }
    });

    if (visibilidadSelect.value === 'roles') {
        rolesContainer.style.display = 'block';
    }

    // Search filter for members
    buscarMiembro.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.miembro-check').forEach(div => {
            const name = div.dataset.name;
            div.style.display = name.includes(term) ? '' : 'none';
        });
    });

    // Auto-select by roles
    function autoSelectByRoles() {
        const checkedRoles = Array.from(document.querySelectorAll('.rol-checkbox:checked')).map(cb => cb.value);
        if (checkedRoles.length === 0) {
            updateMiembrosCount();
            return;
        }

        const params = checkedRoles.map(r => 'roles[]=' + encodeURIComponent(r)).join('&');
        fetch('/tableros/usuarios-por-rol?' + params, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(usuarios => {
            usuarios.forEach(u => {
                const cb = document.getElementById('miembro_' + u.id);
                if (cb) cb.checked = true;
            });
            updateMiembrosCount();
        });
    }

    document.querySelectorAll('.rol-checkbox').forEach(cb => {
        cb.addEventListener('change', autoSelectByRoles);
    });

    // Auto-select by obra
    obraSelect.addEventListener('change', function() {
        const obraId = this.value;
        if (!obraId) {
            updateMiembrosCount();
            return;
        }

        fetch('/tableros/usuarios-por-obra/' + obraId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(usuarios => {
            usuarios.forEach(u => {
                const cb = document.getElementById('miembro_' + u.id);
                if (cb) cb.checked = true;
            });
            updateMiembrosCount();
        });
    });

    // Track manual checkbox changes
    document.querySelectorAll('.miembro-checkbox').forEach(cb => {
        cb.addEventListener('change', updateMiembrosCount);
    });

    // Initial count
    updateMiembrosCount();
});
</script>
@endsection

@push('styles')
<link href="{{ asset('css/tableros.css') }}" rel="stylesheet">
@endpush
