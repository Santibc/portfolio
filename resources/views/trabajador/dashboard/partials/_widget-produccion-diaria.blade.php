@php
    $produccion = $produccion ?? [];
    $hoy = $produccion['hoy'] ?? [];
    $variaciones = $produccion['variaciones'] ?? [];
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

@if(empty($hoy['categorias']))
    <div class="text-center text-muted py-4">Sin datos de producción</div>
@else
    <div class="text-center mb-3">
        <small class="text-muted">{{ $produccion['fecha'] ?? 'Hoy' }}</small>
    </div>

    <div class="row g-2">
        {{-- Categorías dinámicas --}}
        @foreach(($hoy['categorias'] ?? []) as $categoria => $datos)
            @php
                $icono = $iconosCat[$categoria] ?? $iconosCat['otro'];
                $unidad = $unidadesFormato[$datos['unidad']] ?? $datos['unidad'];
                $variacion = $variaciones[$categoria] ?? null;
            @endphp
            <div class="col-6">
                <div class="border rounded p-2 text-center">
                    <i class="bi {{ $icono['icon'] }} text-{{ $icono['color'] }} fs-4"></i>
                    <h5 class="mb-0">{{ number_format($datos['cantidad'], 0, ',', '.') }}</h5>
                    <small class="text-muted">{{ ucfirst($categoria) }} ({{ $unidad }})</small>
                    @if($variacion)
                        <div class="mt-1">
                            @php
                                $icon = $variacion['tipo'] === 'positive' ? 'bi-arrow-up' : ($variacion['tipo'] === 'negative' ? 'bi-arrow-down' : 'bi-dash');
                                $color = $variacion['tipo'] === 'positive' ? 'success' : ($variacion['tipo'] === 'negative' ? 'danger' : 'secondary');
                            @endphp
                            <span class="badge bg-{{ $color }}" style="font-size: 0.65rem;">
                                <i class="bi {{ $icon }}"></i> {{ $variacion['valor'] }}%
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Número de Partes --}}
        <div class="col-6">
            <div class="border rounded p-2 text-center">
                <i class="bi bi-file-text text-secondary fs-4"></i>
                <h5 class="mb-0">{{ $hoy['num_partes'] ?? 0 }}</h5>
                <small class="text-muted">Partes diarios</small>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <small class="text-muted">
            Importe total: <strong>{{ number_format($hoy['importe'] ?? 0, 2, ',', '.') }} €</strong>
        </small>
    </div>
@endif
