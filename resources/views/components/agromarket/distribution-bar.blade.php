@props([
    'category',
    'percentage',
    'variant' => 'staking'
])

<div class="distribution-item">
    <div class="distribution-bar">
        <div class="bar-fill {{ $variant }}" style="width: {{ $percentage }}%"></div>
    </div>
    <div class="distribution-info">
        <span class="category-name">{{ $category }}</span>
        <span class="category-percentage">{{ $percentage }}%</span>
    </div>
</div>
