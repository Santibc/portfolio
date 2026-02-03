@php
    $metricas = $metricas ?? [];
    $pendiente = $metricas['pendiente'] ?? [];
    $porAprobar = $metricas['por_aprobar'] ?? [];
    $aprobada = $metricas['aprobada'] ?? [];

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

@if(empty($metricas))
    <div class="text-center text-muted py-4">Sin datos</div>
@else
    <div class="text-center mb-3">
        <small class="text-muted">{{ $metricas['fecha_inicio'] ?? '' }} - {{ $metricas['fecha_fin'] ?? '' }}</small>
    </div>

    {{-- Tabs de estado --}}
    <ul class="nav nav-pills nav-fill mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-pendiente" type="button">
                Pendiente <span class="badge bg-warning ms-1">{{ $pendiente['num_partes'] ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-por-aprobar" type="button">
                Por Aprobar <span class="badge bg-info ms-1">{{ $porAprobar['num_partes'] ?? 0 }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-aprobada" type="button">
                Aprobado <span class="badge bg-success ms-1">{{ $aprobada['num_partes'] ?? 0 }}</span>
            </button>
        </li>
    </ul>

    {{-- Contenido de tabs --}}
    <div class="tab-content">
        {{-- Tab Pendiente --}}
        <div class="tab-pane fade show active" id="tab-pendiente">
            @if(empty($pendiente['categorias']))
                <div class="text-center text-muted py-3">Sin producción pendiente</div>
            @else
                <div class="row g-2">
                    @foreach($pendiente['categorias'] as $categoria => $datos)
                        @php
                            $icono = $iconosCat[$categoria] ?? $iconosCat['otro'];
                            $unidad = $unidadesFormato[$datos['unidad']] ?? $datos['unidad'];
                        @endphp
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <i class="bi {{ $icono['icon'] }} text-{{ $icono['color'] }} fs-5"></i>
                                <h6 class="mb-0">{{ number_format($datos['cantidad'], 0, ',', '.') }}</h6>
                                <small class="text-muted">{{ ucfirst($categoria) }} ({{ $unidad }})</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <strong>{{ number_format($pendiente['importe_total'], 2, ',', '.') }} €</strong>
                        en {{ $pendiente['num_partes'] }} partes
                    </small>
                </div>
            @endif
        </div>

        {{-- Tab Por Aprobar --}}
        <div class="tab-pane fade" id="tab-por-aprobar">
            @if(empty($porAprobar['categorias']))
                <div class="text-center text-muted py-3">Sin producción por aprobar</div>
            @else
                <div class="row g-2">
                    @foreach($porAprobar['categorias'] as $categoria => $datos)
                        @php
                            $icono = $iconosCat[$categoria] ?? $iconosCat['otro'];
                            $unidad = $unidadesFormato[$datos['unidad']] ?? $datos['unidad'];
                        @endphp
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <i class="bi {{ $icono['icon'] }} text-{{ $icono['color'] }} fs-5"></i>
                                <h6 class="mb-0">{{ number_format($datos['cantidad'], 0, ',', '.') }}</h6>
                                <small class="text-muted">{{ ucfirst($categoria) }} ({{ $unidad }})</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <strong>{{ number_format($porAprobar['importe_total'], 2, ',', '.') }} €</strong>
                        en {{ $porAprobar['num_partes'] }} partes
                    </small>
                </div>
            @endif
        </div>

        {{-- Tab Aprobada --}}
        <div class="tab-pane fade" id="tab-aprobada">
            @if(empty($aprobada['categorias']))
                <div class="text-center text-muted py-3">Sin producción aprobada</div>
            @else
                <div class="row g-2">
                    @foreach($aprobada['categorias'] as $categoria => $datos)
                        @php
                            $icono = $iconosCat[$categoria] ?? $iconosCat['otro'];
                            $unidad = $unidadesFormato[$datos['unidad']] ?? $datos['unidad'];
                        @endphp
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <i class="bi {{ $icono['icon'] }} text-{{ $icono['color'] }} fs-5"></i>
                                <h6 class="mb-0">{{ number_format($datos['cantidad'], 0, ',', '.') }}</h6>
                                <small class="text-muted">{{ ucfirst($categoria) }} ({{ $unidad }})</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <strong>{{ number_format($aprobada['importe_total'], 2, ',', '.') }} €</strong>
                        en {{ $aprobada['num_partes'] }} partes
                    </small>
                </div>
            @endif
        </div>
    </div>
@endif
