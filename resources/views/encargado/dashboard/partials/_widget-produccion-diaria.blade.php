@php
    $produccion = $produccion ?? [];
    $hoy = $produccion['hoy'] ?? [];
    $variaciones = $produccion['variaciones'] ?? [];
@endphp

@php
    $iconosCat = [
        'desbroce' => ['icon' => 'bi-scissors', 'color' => 'success'],
        'limpieza' => ['icon' => 'bi-stars', 'color' => 'info'],
        'herbicida' => ['icon' => 'bi-droplet', 'color' => 'danger'],
        'tala' => ['icon' => 'bi-tree', 'color' => 'warning'],
        'poda' => ['icon' => 'bi-flower1', 'color' => 'primary'],
        'otro' => ['icon' => 'bi-box', 'color' => 'secondary'],
    ];
    $unidadesFormato = [
        'm2' => 'm²',
        'unidades' => 'uds',
        'hectareas' => 'ha',
        'jornal' => 'j',
    ];
@endphp

@if(empty($hoy))
    <div class="text-center text-muted py-4">
        <p class="mb-0">Sin datos de hoy</p>
    </div>
@else
    <div class="text-center mb-3">
        <small class="text-muted">{{ $produccion['fecha'] ?? 'Hoy' }}</small>
    </div>
    <div class="row g-2">
        @foreach(($hoy['categorias'] ?? []) as $categoria => $datos)
            @php
                $icono = $iconosCat[$categoria] ?? $iconosCat['otro'];
                $unidad = $unidadesFormato[$datos['unidad']] ?? $datos['unidad'];
            @endphp
            <div class="col-6">
                <div class="border rounded p-2 text-center">
                    <i class="bi {{ $icono['icon'] }} text-{{ $icono['color'] }} fs-4"></i>
                    <h5 class="mb-0">{{ number_format($datos['cantidad'], 0, ',', '.') }}</h5>
                    <small class="text-muted">{{ ucfirst($categoria) }} ({{ $unidad }})</small>
                    @if(!empty($variaciones[$categoria]))
                        @php
                            $v = $variaciones[$categoria];
                            $vClass = $v['tipo'] === 'positive' ? 'success' : ($v['tipo'] === 'negative' ? 'danger' : 'secondary');
                            $vIcon = $v['tipo'] === 'positive' ? 'bi-arrow-up' : ($v['tipo'] === 'negative' ? 'bi-arrow-down' : 'bi-dash');
                        @endphp
                        <div><span class="badge bg-{{ $vClass }} variation-badge"><i class="bi {{ $vIcon }}"></i> {{ $v['valor'] }}%</span></div>
                    @endif
                </div>
            </div>
        @endforeach
        <div class="col-6">
            <div class="border rounded p-2 text-center">
                <i class="bi bi-file-text text-secondary fs-4"></i>
                <h5 class="mb-0">{{ $hoy['num_partes'] ?? 0 }}</h5>
                <small class="text-muted">Partes</small>
            </div>
        </div>
    </div>
@endif
