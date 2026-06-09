@extends('layouts.app')

@section('title', 'Tipos de Hora')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tipos de Hora</h1>
            <p class="text-muted mb-0">Define los tipos de hora y su precio para las horas extra de los trabajadores</p>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    {{-- Formularios (fuera de la tabla; las filas se asocian por el atributo form) --}}
    @foreach($tipos as $tipo)
        <form id="edit-tipo-{{ $tipo->id }}" action="{{ route('tipos-hora.update', $tipo) }}" method="POST">@csrf @method('PUT')</form>
        <form id="del-tipo-{{ $tipo->id }}" action="{{ route('tipos-hora.destroy', $tipo) }}" method="POST" onsubmit="return confirm('¿Eliminar este tipo de hora?');">@csrf @method('DELETE')</form>
    @endforeach

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Nombre</th><th class="text-end">Precio / hora</th><th class="text-center">Activo</th><th class="text-center">Bonos</th><th class="text-end">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse($tipos as $tipo)
                        <tr>
                            <td><input type="text" name="nombre" form="edit-tipo-{{ $tipo->id }}" class="form-control form-control-sm" value="{{ $tipo->nombre }}" required></td>
                            <td>
                                <div class="input-group input-group-sm" style="max-width:160px; margin-left:auto;">
                                    <input type="number" name="precio_hora" form="edit-tipo-{{ $tipo->id }}" class="form-control" step="0.01" min="0" value="{{ $tipo->precio_hora }}" required>
                                    <span class="input-group-text">€</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" form="edit-tipo-{{ $tipo->id }}" {{ $tipo->activo ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $tipo->bonos_count }}</span></td>
                            <td class="text-end">
                                <button type="submit" form="edit-tipo-{{ $tipo->id }}" class="btn btn-sm btn-outline-primary" title="Guardar cambios"><i class="bi bi-check-lg"></i></button>
                                <button type="submit" form="del-tipo-{{ $tipo->id }}" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No hay tipos de hora. Añade el primero abajo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Añadir nuevo --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-plus-lg me-2"></i>Añadir tipo de hora</h5></div>
        <div class="card-body">
            <form action="{{ route('tipos-hora.store') }}" method="POST">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Hora extra festiva" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Precio / hora</label>
                        <div class="input-group">
                            <input type="number" name="precio_hora" class="form-control" step="0.01" min="0" placeholder="0,00" required>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" checked id="nuevoActivo">
                            <label class="form-check-label" for="nuevoActivo">Activo</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Añadir</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
