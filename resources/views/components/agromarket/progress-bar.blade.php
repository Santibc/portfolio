@props([
    'percentage',
    'showText' => true
])

<div class="progress-container">
    <div class="progress-bar">
        <div class="progress-fill" style="width: {{ $percentage }}%"></div>
    </div>
    @if($showText)
        <span class="progress-text">{{ $percentage }}%</span>
    @endif
</div>
