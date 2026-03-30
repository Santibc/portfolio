@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/tableros.css') }}" rel="stylesheet">
@endpush

@php
    $hex = ltrim($tablero->color_fondo ?? '#3B5998', '#');
    if(strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $boardLuminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    $headerTextColor = $boardLuminance > 0.6 ? '#172b4d' : 'white';
    $headerBadgeBg = $boardLuminance > 0.6 ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.25)';
    $headerBtnBg = $boardLuminance > 0.6 ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.2)';
    $headerOverlay = $boardLuminance > 0.6 ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.15)';
    $headerTextShadow = $boardLuminance > 0.6 ? 'none' : '0 1px 3px rgba(0,0,0,0.3)';
    $scrollbarColor = $boardLuminance > 0.6 ? 'rgba(0,0,0,0.2)' : 'rgba(255,255,255,0.3)';
@endphp

@section('content')
<div class="tablero-wrapper {{ $boardLuminance > 0.6 ? 'light-board' : '' }}" style="background-color: {{ $tablero->color_fondo }};@if($tablero->imagen_fondo) background-image: url('{{ asset('uploads/' . $tablero->imagen_fondo) }}'); background-size: cover; background-position: center;@endif --header-text: {{ $headerTextColor }}; --header-badge-bg: {{ $headerBadgeBg }}; --header-btn-bg: {{ $headerBtnBg }}; --header-overlay: {{ $headerOverlay }}; --header-text-shadow: {{ $headerTextShadow }}; --scrollbar-color: {{ $scrollbarColor }};" id="tableroWrapper">

    {{-- Board Header --}}
    <div class="tablero-header" style="background: {{ $headerOverlay }};">
        <div class="tablero-header-left">
            <a href="{{ route('tableros.index') }}" class="btn-tablero" style="padding:4px 10px; background: {{ $headerBtnBg }}; color: {{ $headerTextColor }};">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="tablero-titulo" style="color: {{ $headerTextColor }}; text-shadow: {{ $headerTextShadow }};">{{ $tablero->nombre }}</h1>
            <span class="badge" style="background:{{ $headerBadgeBg }};color:{{ $headerTextColor }};font-size:0.7rem;">
                @if($tablero->visibilidad === 'todos')
                    <i class="bi bi-globe2"></i> Publico
                @elseif($tablero->visibilidad === 'roles')
                    <i class="bi bi-people"></i> Por roles
                @else
                    <i class="bi bi-lock"></i> Miembros
                @endif
            </span>
            @if($tablero->obra)
            <span class="badge" style="background:{{ $headerBadgeBg }};color:{{ $headerTextColor }};font-size:0.7rem;">
                <i class="bi bi-building"></i> {{ $tablero->obra->codigo }} - {{ $tablero->obra->nombre }}
            </span>
            @endif
        </div>
        <div class="tablero-header-right">
            <button class="btn-tablero" id="btnFiltros" onclick="toggleFiltros()" style="background: {{ $headerBtnBg }}; color: {{ $headerTextColor }};">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            <button class="btn-tablero" data-bs-toggle="modal" data-bs-target="#miembrosModal" style="background: {{ $headerBtnBg }}; color: {{ $headerTextColor }};">
                <i class="bi bi-people"></i>
                <span class="d-none d-md-inline">{{ $tablero->miembros->count() }}</span>
            </button>
            @if($puedeEditar)
            <a href="{{ route('tableros.edit', $tablero) }}" class="btn-tablero" style="background: {{ $headerBtnBg }}; color: {{ $headerTextColor }};">
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
        <button class="btn btn-sm" style="background:{{ $headerBtnBg }};color:{{ $headerTextColor }};" onclick="limpiarFiltros()">
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
