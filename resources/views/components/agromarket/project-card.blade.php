@props([
    'title',
    'location',
    'image' => null,
    'category',
    'roi',
    'duration',
    'funded',
    'target',
    'investors' => 0,
    'status' => 'active',
    'featured' => false,
    'projectId' => null
])

@php
    // Evitar división por cero
    $progress = $target > 0 ? ($funded / $target) * 100 : 0;

    // Calcular monto recaudado
    $raisedAmount = ($target * $funded) / 100;

    // Variantes de badges de categoría
    $categoryVariants = [
        'staking' => 'STAKING',
        'lending' => 'LENDING',
        'crowdfunding' => 'CROWDFUNDING',
        'defi' => 'DeFi'
    ];

    $categoryLabel = $categoryVariants[$category] ?? strtoupper($category);
@endphp

<div class="project-card {{ $featured ? 'featured' : '' }}">
    <div class="project-image" @if($image) style="background-image: url('{{ $image }}')" @endif>
        @if(!$image)
            <i class="fas fa-seedling"></i>
        @endif
        @if($featured)
            <div class="project-badge">DESTACADO</div>
        @endif
        <div class="project-category-badge">
            <x-agromarket.badge variant="{{ $category }}" type="category">{{ $categoryLabel }}</x-agromarket.badge>
        </div>
    </div>
    <div class="project-content">
        <h3 class="project-name">{{ $title }}</h3>
        <p class="project-location">
            <i class="fas fa-map-marker-alt"></i> {{ $location }}
        </p>

        <div class="project-metrics">
            <div class="metric">
                <span class="metric-label">ROI</span>
                <span class="metric-value success">{{ $roi }}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Plazo</span>
                <span class="metric-value">{{ $duration }}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Inversores</span>
                <span class="metric-value">{{ $investors }}</span>
            </div>
        </div>

        <div class="progress-section">
            <div class="progress-header">
                <span class="progress-label">Financiamiento</span>
                <span class="progress-percentage">{{ round($progress) }}%</span>
            </div>
            <x-agromarket.progress-bar :percentage="$progress" />
            <div class="progress-footer">
                <span class="raised-amount">${{ number_format($raisedAmount, 0) }}</span>
                <span class="target-amount">de ${{ number_format($target, 0) }}</span>
            </div>
        </div>

        <div class="project-actions">
            @if($projectId)
                <x-agromarket.button variant="primary" onclick="window.location.href='{{ route('proyectos.show', $projectId) }}'">
                    Ver Proyecto
                </x-agromarket.button>
            @else
                <x-agromarket.button variant="primary">
                    Ver Proyecto
                </x-agromarket.button>
            @endif
        </div>
    </div>
</div>
