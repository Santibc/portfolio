{{-- Seccion: Historial de Entregas --}}
@if($orden->entregas->count() > 0)
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-box-arrow-right me-2 text-primary"></i>Entregas ({{ $orden->entregas->count() }})</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2">
        @foreach($orden->entregas->sortByDesc('created_at') as $entrega)
            <div class="{{ !$loop->first ? 'border-top pt-3' : '' }} {{ !$loop->last ? 'mb-3' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="small fw-semibold text-dark">{{ $entrega->created_at->format('d/m/Y H:i') }}</span>
                        <span class="small text-muted ms-2">
                            <i class="bi bi-person me-1"></i>{{ $entrega->entregadaPorUsuario->name ?? '-' }}
                        </span>
                    </div>
                </div>

                {{-- Piezas entregadas --}}
                <div class="mb-2">
                    @foreach($entrega->piezas as $ep)
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary">{{ $ep->cantidad }}</span>
                            <span class="small">{{ $ep->ordenPieza->nombre ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Fotos de la entrega --}}
                @if($entrega->fotos->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($entrega->fotos as $foto)
                            <img src="{{ asset($foto->ruta_miniatura ?? $foto->ruta_archivo) }}"
                                 class="border rounded"
                                 style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                 onclick="abrirLightbox('{{ $foto->ruta_archivo }}', 'Foto Entrega')"
                                 title="Ver foto de entrega">
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif
