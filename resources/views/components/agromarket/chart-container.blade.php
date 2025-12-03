@props([
    'title' => null,
    'chartId',
    'value' => null,
    'change' => null,
    'height' => 300
])

<div class="chart-container" style="position: relative; height: {{ $height }}px;">
    @if($title)
        <div class="chart-header">
            <h3>{{ $title }}</h3>
            @if($value)
                <div class="chart-info">
                    <span class="chart-value">{{ $value }}</span>
                    @if($change)
                        <span class="chart-change positive">{{ $change }}</span>
                    @endif
                </div>
            @endif
        </div>
    @endif
    <div class="chart-content" style="position: relative; height: 100%;">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>
