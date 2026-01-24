{{-- Widget: Producción del Mes --}}
@php
    $produccion = $produccion ?? [];
    $actual = $produccion['actual'] ?? [];
    $variaciones = $produccion['variaciones'] ?? [];
@endphp

@if(empty($actual))
    <div class="text-center text-muted py-4">Sin datos de producción</div>
@else
    <div class="text-center mb-3">
        <span class="text-muted">{{ $produccion['periodo'] ?? 'Mes actual' }}</span>
    </div>

    <div class="row g-3">
        {{-- Desbroce --}}
        <div class="col-6">
            <div class="border rounded p-3 text-center">
                <i class="bi bi-rulers text-primary fs-3 d-block mb-2"></i>
                <h4 class="mb-0">{{ number_format($actual['desbroce_m2'] ?? 0, 0, ',', '.') }}</h4>
                <small class="text-muted">m² Desbroce</small>
                @if(!empty($variaciones['desbroce']))
                    <div class="mt-1">
                        @php
                            $v = $variaciones['desbroce'];
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

        {{-- Talas --}}
        <div class="col-6">
            <div class="border rounded p-3 text-center">
                <i class="bi bi-tree text-success fs-3 d-block mb-2"></i>
                <h4 class="mb-0">{{ $actual['talas'] ?? 0 }}</h4>
                <small class="text-muted">Talas</small>
                @if(!empty($variaciones['talas']))
                    <div class="mt-1">
                        @php
                            $v = $variaciones['talas'];
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

        {{-- Podas --}}
        <div class="col-6">
            <div class="border rounded p-3 text-center">
                <i class="bi bi-scissors text-info fs-3 d-block mb-2"></i>
                <h4 class="mb-0">{{ $actual['podas'] ?? 0 }}</h4>
                <small class="text-muted">Podas</small>
            </div>
        </div>

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
