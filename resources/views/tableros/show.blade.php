@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/tableros.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="tablero-wrapper" style="background-color: {{ $tablero->color_fondo }};" id="tableroWrapper">

    {{-- Board Header --}}
    <div class="tablero-header">
        <div class="tablero-header-left">
            <a href="{{ route('tableros.index') }}" class="btn-tablero" style="padding:4px 10px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="tablero-titulo">{{ $tablero->nombre }}</h1>
            <span class="badge" style="background:rgba(255,255,255,0.25);color:white;font-size:0.7rem;">
                @if($tablero->visibilidad === 'todos')
                    <i class="bi bi-globe2"></i> Publico
                @elseif($tablero->visibilidad === 'roles')
                    <i class="bi bi-people"></i> Por roles
                @else
                    <i class="bi bi-lock"></i> Miembros
                @endif
            </span>
            @if($tablero->obra)
            <span class="badge" style="background:rgba(255,255,255,0.25);color:white;font-size:0.7rem;">
                <i class="bi bi-building"></i> {{ $tablero->obra->codigo }} - {{ $tablero->obra->nombre }}
            </span>
            @endif
        </div>
        <div class="tablero-header-right">
            <button class="btn-tablero" id="btnFiltros" onclick="toggleFiltros()">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            <button class="btn-tablero" data-bs-toggle="modal" data-bs-target="#miembrosModal">
                <i class="bi bi-people"></i>
                <span class="d-none d-md-inline">{{ $tablero->miembros->count() }}</span>
            </button>
            @if($puedeEditar)
            <a href="{{ route('tableros.edit', $tablero) }}" class="btn-tablero">
                <i class="bi bi-gear"></i>
            </a>
            @endif
        </div>
    </div>

    {{-- Filters Panel --}}
    <div class="tablero-filtros" id="filtrosPanel" style="display:none;">
        <select class="form-select form-select-sm" id="filtroMiembro" onchange="aplicarFiltros()">
            <option value="">Todos los miembros</option>
            @foreach($tablero->miembros as $m)
            <option value="{{ $m->id }}">{{ $m->name }}</option>
            @endforeach
        </select>
        <select class="form-select form-select-sm" id="filtroEtiqueta" onchange="aplicarFiltros()">
            <option value="">Todas las etiquetas</option>
            @foreach($tablero->etiquetas as $e)
            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
            @endforeach
        </select>
        <select class="form-select form-select-sm" id="filtroPrioridad" onchange="aplicarFiltros()">
            <option value="">Todas las prioridades</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
        </select>
        <select class="form-select form-select-sm" id="filtroFecha" onchange="aplicarFiltros()">
            <option value="">Cualquier fecha</option>
            <option value="vencida">Vencidas</option>
            <option value="pronto">Proximas</option>
            <option value="sin_fecha">Sin fecha</option>
        </select>
        <input type="text" class="form-control form-control-sm" id="filtroBusqueda"
               placeholder="Buscar tarjeta..." oninput="aplicarFiltros()" style="max-width:200px;">
        <button class="btn btn-sm" style="background:rgba(255,255,255,0.3);color:white;" onclick="limpiarFiltros()">
            <i class="bi bi-x-lg"></i> Limpiar
        </button>
    </div>

    {{-- Columns Container --}}
    <div class="tablero-columnas" id="columnasContainer" data-tablero-id="{{ $tablero->id }}">
        @foreach($tablero->columnas as $columna)
            @include('tableros.partials._column', ['columna' => $columna, 'puedeEditar' => $puedeEditar])
        @endforeach

        {{-- Add column form --}}
        @if($puedeEditar)
        <div class="tablero-columna-nueva">
            <form id="formNuevaColumna" onsubmit="crearColumna(event)">
                <input type="text" class="form-control form-control-sm" name="nombre"
                       placeholder="Nombre de la lista..." required>
                <button type="submit" class="btn btn-primary btn-sm mt-2 w-100">
                    <i class="bi bi-plus-lg"></i> Agregar lista
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Card Detail Modal --}}
@include('tableros.partials._card-modal')

{{-- Members Modal --}}
@include('tableros.partials._members-modal')

{{-- Labels Modal --}}
@include('tableros.partials._labels-modal')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/tableros.js') }}"></script>
<script>
    window.TABLERO_ID = {{ $tablero->id }};
    window.PUEDE_EDITAR = {{ $puedeEditar ? 'true' : 'false' }};
    window.TABLERO_ETIQUETAS = @json($tablero->etiquetas);
    window.TABLERO_MIEMBROS = @json($miembrosJson);
    window.CURRENT_USER_ID = {{ auth()->id() }};
</script>
@endpush
