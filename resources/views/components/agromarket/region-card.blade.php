@props([
    'region',
    'projectCount',
    'projects' => [], // Array de proyectos: ['name' => '', 'icon' => '', 'roi' => '']
    'showMultiple' => false, // Si true, muestra iconos múltiples
    'avgRoi' => null
])

<div class="region-card">
    <div class="region-header">
        <h4>{{ $region }}</h4>
        <span class="project-count">{{ $projectCount }} {{ $projectCount == 1 ? 'proyecto' : 'proyectos' }}</span>
    </div>

    @if($showMultiple && count($projects) > 1)
        <div class="region-projects-mini">
            @foreach($projects as $project)
                <span class="mini-icon">{{ $project['icon'] }}</span>
            @endforeach
        </div>
        @if($avgRoi)
            <div class="avg-roi">ROI promedio: {{ $avgRoi }}%</div>
        @endif
    @else
        @foreach($projects as $project)
            <div class="region-project">
                <div class="project-icon">{{ $project['icon'] }}</div>
                <div class="project-info">
                    <span class="project-name">{{ $project['name'] }}</span>
                    <span class="project-roi">ROI: {{ $project['roi'] }}%</span>
                </div>
            </div>
        @endforeach
    @endif
</div>
