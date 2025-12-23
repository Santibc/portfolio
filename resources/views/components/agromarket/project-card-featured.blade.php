@props([
    'projectName',
    'location',
    'icon' => 'fas fa-seedling',
    'roi',
    'duration',
    'minInvestment',
    'raised',
    'goal',
    'featured' => false
])

@php
    $progress = $goal > 0 ? ($raised / $goal) * 100 : 0;
@endphp

<div class="project-card {{ $featured ? 'featured' : '' }}">
    <div class="project-image">
        <i class="{{ $icon }}"></i>
        @if($featured)
            <div class="project-badge">DESTACADO</div>
        @endif
    </div>
    <div class="project-content">
        <h3 class="project-name">{{ $projectName }}</h3>
        <p class="project-location">{{ $location }}</p>
        <div class="project-metrics">
            <div class="metric">
                <span class="metric-label">ROI</span>
                <span class="metric-value success">{{ $roi }}% E.A</span>
            </div>
            <div class="metric">
                <span class="metric-label">Plazo</span>
                <span class="metric-value">{{ $duration }}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Mín.</span>
                <span class="metric-value">${{ number_format($minInvestment, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="progress-container">
            <div class="progress-info">
                <span>Recaudado: ${{ number_format($raised, 0) }} / ${{ number_format($goal, 0) }}</span>
                <span>{{ round($progress) }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $progress }}%;"></div>
            </div>
        </div>
        <div class="project-actions">
            <button class="btn-primary">Invertir Ahora</button>
            <button class="btn-outline">Ver Detalles</button>
        </div>
    </div>
</div>
