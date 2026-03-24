@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--manzer-primary);">
                <i class="bi bi-kanban me-2"></i>Tableros
            </h1>
            <p class="text-muted mb-0">Organiza tareas y proyectos con tableros estilo Kanban</p>
        </div>
        @can('crear_tableros')
        <a href="{{ route('tableros.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Tablero
        </a>
        @endcan
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:rgba(30,64,175,0.1);">
                        <i class="bi bi-kanban" style="font-size:1.3rem;color:var(--manzer-primary);"></i>
                    </div>
                    <div>
                        <div class="fw-bold h4 mb-0">{{ $tableros->count() }}</div>
                        <div class="text-muted small">Tableros activos</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:rgba(16,185,129,0.1);">
                        <i class="bi bi-person-check" style="font-size:1.3rem;color:#10b981;"></i>
                    </div>
                    <div>
                        <div class="fw-bold h4 mb-0">{{ $tableros->filter(fn($t) => $t->miembros->contains('id', auth()->id()))->count() }}</div>
                        <div class="text-muted small">Mis tableros</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:rgba(245,158,11,0.1);">
                        <i class="bi bi-card-checklist" style="font-size:1.3rem;color:#f59e0b;"></i>
                    </div>
                    <div>
                        <div class="fw-bold h4 mb-0">{{ $tableros->sum('tarjetas_count') }}</div>
                        <div class="text-muted small">Tarjetas totales</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Board Grid --}}
    @if($tableros->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-kanban" style="font-size: 4rem; color: #ddd;"></i>
            <h4 class="mt-3 text-muted">No hay tableros disponibles</h4>
            <p class="text-muted">Crea un nuevo tablero para comenzar a organizar tareas.</p>
            @can('crear_tableros')
            <a href="{{ route('tableros.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg me-1"></i> Crear primer tablero
            </a>
            @endcan
        </div>
    @else
        <div class="tablero-grid">
            @foreach($tableros as $tablero)
            @php
                $hex = ltrim($tablero->color_fondo ?? '#3B5998', '#');
                if(strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
                $cardTextColor = $luminance > 0.6 ? '#172b4d' : 'white';
                $cardTextShadow = $luminance > 0.6 ? 'none' : '0 1px 3px rgba(0,0,0,0.4)';
            @endphp
            <a href="{{ route('tableros.show', $tablero) }}" class="tablero-card">
                <div class="tablero-card-header" style="background: {{ $tablero->color_fondo }};@if($tablero->imagen_fondo) background-image: url('{{ asset('uploads/' . $tablero->imagen_fondo) }}'); background-size: cover; background-position: center;@endif">
                    <span class="tablero-card-nombre" style="color: {{ $cardTextColor }}; text-shadow: 0 1px 3px rgba(0,0,0,0.4);">{{ $tablero->nombre }}</span>
                </div>
                <div class="tablero-card-body">
                    <div class="tablero-card-meta">
                        <span>
                            <i class="bi bi-card-checklist me-1"></i>{{ $tablero->tarjetas_count ?? 0 }} tarjetas
                        </span>
                        <div class="tablero-card-avatars">
                            @foreach($tablero->miembros->take(4) as $miembro)
                            <div class="avatar-mini" title="{{ $miembro->name }}">
                                @if($miembro->hasProfilePhoto())
                                    <img src="{{ $miembro->profile_photo_url }}" alt="{{ $miembro->initials }}">
                                @else
                                    {{ $miembro->initials }}
                                @endif
                            </div>
                            @endforeach
                            @if($tablero->miembros->count() > 4)
                            <div class="avatar-mini avatar-more">+{{ $tablero->miembros->count() - 4 }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('styles')
<link href="{{ asset('css/tableros.css') }}" rel="stylesheet">
@endpush
