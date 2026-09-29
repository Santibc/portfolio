@props([
    'id' => 'chart-' . uniqid(),
    'type' => 'line',     // line | bar | area | donut | pie | radial
    'series' => [],
    'options' => [],
    'height' => 320,
])

@php
    $defaultOptions = [
        'chart' => [
            'type' => $type,
            'height' => $height,
            'width' => '100%',
            'parentHeightOffset' => 0,
            'fontFamily' => 'Plus Jakarta Sans, Inter, sans-serif',
            'toolbar' => ['show' => false],
            'animations' => ['enabled' => true, 'easing' => 'easeinout', 'speed' => 600],
        ],
        'colors' => ['#e4550a', '#d8a654', '#a4672a', '#fda571', '#852e12'],
        'stroke' => ['curve' => 'smooth', 'width' => 3],
        'grid' => ['borderColor' => '#ecd6b3', 'strokeDashArray' => 4],
        'dataLabels' => ['enabled' => false],
        'series' => $series,
    ];
    $merged = array_replace_recursive($defaultOptions, $options);
@endphp

<div id="{{ $id }}" class="w-full max-w-full min-w-0 overflow-hidden min-h-[200px]"></div>

<script>
    (function () {
        var opts = {!! json_encode($merged, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
        function tryRender() {
            if (!window.makeChart) { return setTimeout(tryRender, 50); }
            window.makeChart('#{{ $id }}', opts);
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', tryRender);
        } else {
            tryRender();
        }
    })();
</script>
