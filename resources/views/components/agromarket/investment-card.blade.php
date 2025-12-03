@props([
    'projectId',
    'projectName',
    'location',
    'icon' => 'fas fa-seedling',
    'invested',
    'roi',
    'profit',
    'progress',
    'category',
    'status'
])

@php
    $roiClass = $roi >= 0 ? 'positive' : 'negative';
@endphp

<div class="investment-card">
    <div class="investment-card-header">
        <div class="investment-card-icon">
            <i class="{{ $icon }}"></i>
        </div>
        <div class="investment-card-actions">
            <button class="investment-action-btn" onclick="viewDetails('{{ $projectId }}')" title="Ver detalles">
                <i class="fas fa-eye"></i>
            </button>
            <button class="investment-action-btn" onclick="openTrading('{{ $projectId }}')" title="Trading">
                <i class="fas fa-exchange-alt"></i>
            </button>
        </div>
    </div>
    <div class="investment-card-body">
        <h4 class="investment-card-title">{{ $projectName }}</h4>
        <p class="investment-card-location">
            <i class="fas fa-map-marker-alt"></i>
            {{ $location }}
        </p>
        <div class="investment-metrics">
            <div class="investment-metric">
                <span class="investment-metric-label">Invertido</span>
                <span class="investment-metric-value">${{ number_format($invested, 0) }}</span>
            </div>
            <div class="investment-metric">
                <span class="investment-metric-label">ROI</span>
                <span class="investment-metric-value {{ $roiClass }}">{{ $roi >= 0 ? '+' : '' }}{{ $roi }}%</span>
            </div>
            <div class="investment-metric">
                <span class="investment-metric-label">Ganancia</span>
                <span class="investment-metric-value">${{ number_format($profit, 0) }}</span>
            </div>
        </div>
        <div class="investment-progress-section">
            <div class="investment-progress-header">
                <span>Progreso</span>
                <span>{{ $progress }}%</span>
            </div>
            <div class="investment-progress-bar">
                <div class="investment-progress-fill" style="width: {{ $progress }}%; background: #28a745"></div>
            </div>
        </div>
        <div class="investment-card-footer">
            <x-agromarket.badge variant="{{ $category }}" type="category">{{ strtoupper($category) }}</x-agromarket.badge>
            <x-agromarket.badge variant="{{ $status }}" type="status">{{ ucfirst($status) }}</x-agromarket.badge>
        </div>
    </div>
</div>
