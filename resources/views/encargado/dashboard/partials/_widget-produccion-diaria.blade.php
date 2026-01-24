@php
    $produccion = $produccion ?? [];
    $hoy = $produccion['hoy'] ?? [];
    $variaciones = $produccion['variaciones'] ?? [];
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
        <div class="col-6">
            <div class="border rounded p-2 text-center">
                <i class="bi bi-rulers text-primary fs-4"></i>
                <h5 class="mb-0">{{ number_format($hoy['desbroce_m2'] ?? 0, 0, ',', '.') }}</h5>
                <small class="text-muted">m²</small>
                @if(!empty($variaciones['desbroce']))
                    @php
                        $v = $variaciones['desbroce'];
                        $vClass = $v['tipo'] === 'positive' ? 'success' : ($v['tipo'] === 'negative' ? 'danger' : 'secondary');
                        $vIcon = $v['tipo'] === 'positive' ? 'bi-arrow-up' : ($v['tipo'] === 'negative' ? 'bi-arrow-down' : 'bi-dash');
                    @endphp
                    <div><span class="badge bg-{{ $vClass }} variation-badge"><i class="bi {{ $vIcon }}"></i> {{ $v['valor'] }}%</span></div>
                @endif
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-2 text-center">
                <i class="bi bi-tree text-success fs-4"></i>
                <h5 class="mb-0">{{ $hoy['talas'] ?? 0 }}</h5>
                <small class="text-muted">Talas</small>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-2 text-center">
                <i class="bi bi-scissors text-info fs-4"></i>
                <h5 class="mb-0">{{ $hoy['podas'] ?? 0 }}</h5>
                <small class="text-muted">Podas</small>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-2 text-center">
                <i class="bi bi-file-text text-secondary fs-4"></i>
                <h5 class="mb-0">{{ $hoy['num_partes'] ?? 0 }}</h5>
                <small class="text-muted">Partes</small>
            </div>
        </div>
    </div>
@endif
