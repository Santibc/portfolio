@extends('layouts.app')

@section('title', 'Panel del Operario')

@section('content')
<div class="container-fluid py-4">
    <x-sinden.page-header title="Panel del Operario" :description="'Bienvenido, ' . auth()->user()->name . ' | ' . now()->translatedFormat('l d \\d\\e F Y')">
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-list-check" :value="$stats['ordenes_asignadas']" title="Ordenes Asignadas" color="primary" />
        <x-sinden.stat-card icon="bi bi-gear-wide-connected" :value="$stats['piezas_en_proceso']" title="Piezas en Proceso" color="warning" />
        <x-sinden.stat-card icon="bi bi-plus-circle" :value="$stats['para_complementar']" title="Para Complementar" color="info" />
        <x-sinden.stat-card icon="bi bi-check-circle" :value="$stats['completadas_hoy']" title="Completadas Hoy" color="success" />
        <x-sinden.stat-card icon="bi bi-shield-check" :value="$stats['garantias_pendientes']" title="Garantias Pendientes" color="danger" />
    </div>

    {{-- Quick Actions --}}
    <div class="quick-actions mt-4">
        <a href="{{ route('operario.ordenes-asignadas') }}" class="quick-action-btn">
            <i class="bi bi-list-check"></i>
            <span>Ver Ordenes Asignadas</span>
            @if($stats['ordenes_asignadas'] > 0)
                <span class="badge bg-primary">{{ $stats['ordenes_asignadas'] }}</span>
            @endif
        </a>
        <a href="{{ route('operario.buscar') }}" class="quick-action-btn">
            <i class="bi bi-search"></i>
            <span>Buscar Orden</span>
        </a>
        <a href="{{ route('operario.complementar') }}" class="quick-action-btn">
            <i class="bi bi-plus-circle"></i>
            <span>Complementar Ordenes</span>
            @if($stats['para_complementar'] > 0)
                <span class="badge bg-info">{{ $stats['para_complementar'] }}</span>
            @endif
        </a>
        <a href="{{ route('operario.garantias') }}" class="quick-action-btn">
            <i class="bi bi-shield-check"></i>
            <span>Mis Garantias</span>
            @if($stats['garantias_pendientes'] > 0)
                <span class="badge bg-danger">{{ $stats['garantias_pendientes'] }}</span>
            @endif
        </a>
    </div>
</div>
@endsection
