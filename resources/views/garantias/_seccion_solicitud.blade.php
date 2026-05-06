@php
    $garantiasPendientes = $garantiasPendientes ?? collect();
    $garantiasLiberadasEnEsta = $garantiasLiberadasEnEsta ?? collect();
    $puedeLiberar = $puedeLiberar ?? false;
    $solicitudId = $solicitudId ?? null;
@endphp

@if($garantiasPendientes->isNotEmpty() || $garantiasLiberadasEnEsta->isNotEmpty())
<div class="row mt-3">
    <div class="col-md-12">
        <h6 class="border-bottom pb-2"><i class="bi bi-shield-check"></i> Garantías</h6>

        @if($garantiasPendientes->isNotEmpty())
            <div class="alert alert-warning py-2 mb-2">
                <strong>Garantías pendientes del cliente:</strong> {{ $garantiasPendientes->count() }}
            </div>
            @foreach($garantiasPendientes as $g)
                <div class="card mb-2 border-warning">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <strong>{{ $g->producto?->nombre ?? '—' }}</strong>@if($g->variante && $g->variante->nombre_variante) — {{ $g->variante->nombre_variante }}@endif
                                <span class="badge bg-warning text-dark ms-2">Pendiente</span>
                                <div class="small text-muted">{{ $g->tipoLegible() }} · Registrada el {{ $g->created_at?->format('d/m/Y H:i') }}</div>
                                <div class="mt-1">
                                    @forelse($g->documentos as $doc)
                                        <a href="{{ asset($doc->ruta_relativa) }}" class="btn btn-sm btn-outline-primary me-1 mb-1" target="_blank" download="{{ $doc->nombre_original }}">
                                            <i class="bi bi-download"></i> {{ $doc->nombre_original }}
                                        </a>
                                    @empty
                                        <span class="text-muted small">Sin documentos</span>
                                    @endforelse
                                </div>
                            </div>
                            @if($puedeLiberar)
                                <button type="button" class="btn btn-sm btn-success btn-liberar-garantia-solicitud"
                                        data-garantia-id="{{ $g->id }}"
                                        data-solicitud-id="{{ $solicitudId }}">
                                    <i class="bi bi-unlock"></i> Liberar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        @if($garantiasLiberadasEnEsta->isNotEmpty())
            <div class="alert alert-success py-2 mb-2 mt-3">
                <strong>Garantías liberadas en esta cotización:</strong> {{ $garantiasLiberadasEnEsta->count() }}
            </div>
            @foreach($garantiasLiberadasEnEsta as $g)
                <div class="card mb-2 border-success">
                    <div class="card-body py-2">
                        <strong>{{ $g->producto?->nombre ?? '—' }}</strong>@if($g->variante && $g->variante->nombre_variante) — {{ $g->variante->nombre_variante }}@endif
                        <span class="badge bg-success ms-2">Liberada</span>
                        <div class="small text-muted">{{ $g->tipoLegible() }}</div>
                        <div class="small mt-1"><strong>Observación:</strong> {{ $g->observacion_liberacion }}</div>
                        <div class="small text-muted">Liberada por {{ $g->usuarioLiberador?->name ?? '—' }} el {{ $g->liberado_en?->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endif
