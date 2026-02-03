{{-- Widget: Producción del Mes --}}
@php
    $produccion = $produccion ?? [];
    $actual = $produccion['actual'] ?? [];
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

@if(empty($actual))
    <div class="text-center text-muted py-4">Sin datos de producción</div>
@else
    <div class="text-center mb-3">
        <span class="text-muted">{{ $produccion['periodo'] ?? 'Mes actual' }}</span>
    </div>

    <div class="row g-3">
        {{-- Categorías dinámicas --}}
        @foreach(($actual['categorias'] ?? []) as $categoria => $datos)
            @php
                $icono = $iconosCat[$categoria] ?? $iconosCat['otro'];
                $unidad = $unidadesFormato[$datos['unidad']] ?? $datos['unidad'];
            @endphp
            <div class="col-6">
                <div class="border rounded p-3 text-center">
                    <i class="bi {{ $icono['icon'] }} text-{{ $icono['color'] }} fs-3 d-block mb-2"></i>
                    <h4 class="mb-0">{{ number_format($datos['cantidad'], 0, ',', '.') }}</h4>
                    <small class="text-muted">{{ ucfirst($categoria) }} ({{ $unidad }})</small>
                    @if(!empty($variaciones[$categoria]))
                        <div class="mt-1">
                            @php
                                $v = $variaciones[$categoria];
                                $icon = $v['tipo'] === 'positive' ? 'bi-arrow-up' : ($v['tipo'] === 'negative' ? 'bi-arrow-down' : 'bi-dash');
                                $color = $v['tipo'] === 'positive' ? 'success' : ($v['tipo'] === 'negative' ? 'danger' : 'secondary');
                            @endphp
                            <span class="badge bg-{{ $color }}" style="font-size: 0.7rem;">
                                <i class="bi {{ $icon }}"></i> {{ $v['valor'] }}%
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Importe Producido --}}
        <div class="col-6">
            <div class="border rounded p-3 text-center">
                <i class="bi bi-currency-euro text-warning fs-3 d-block mb-2"></i>
                <h4 class="mb-0">{{ number_format($actual['importe_total'] ?? 0, 0, ',', '.') }} €</h4>
                <small class="text-muted">Producido</small>
                @if(!empty($variaciones['importe']))
                    <div class="mt-1">
                        @php
                            $v = $variaciones['importe'];
                            $icon = $v['tipo'] === 'positive' ? 'bi-arrow-up' : ($v['tipo'] === 'negative' ? 'bi-arrow-down' : 'bi-dash');
                            $color = $v['tipo'] === 'positive' ? 'success' : ($v['tipo'] === 'negative' ? 'danger' : 'secondary');
                        @endphp
                        <span class="badge bg-{{ $color }}" style="font-size: 0.7rem;">
                            <i class="bi {{ $icon }}"></i> {{ $v['valor'] }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <small class="text-muted">{{ $actual['num_partes'] ?? 0 }} partes diarios procesados</small>
    </div>
@endif
