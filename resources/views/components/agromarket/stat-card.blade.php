@props([
    'icon',
    'value',
    'label' => null,
    'title' => null,
    'description' => null,
    'change' => null,
    'changeType' => 'neutral',
    'variant' => 'primary',
    'color' => 'primary'
])

<?php
// Usar 'title' y 'description' si están disponibles, sino usar 'label'
$cardTitle = $title ?? $label ?? '';
$cardDescription = $description ?? ($change ? '' : '');
$cardVariant = $color ?? $variant;
?>

<div class="summary-card {{ $cardVariant }}">
    <div class="card-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <div class="card-content">
        <h3 class="card-value">{{ $value }}</h3>
        <p class="card-label">{{ $cardTitle }}</p>
        @if($cardDescription)
            <p class="card-description" style="font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem;">{{ $cardDescription }}</p>
        @endif
        @if($change)
            <span class="card-change {{ $changeType }}">{{ $change }}</span>
        @endif
    </div>
</div>
