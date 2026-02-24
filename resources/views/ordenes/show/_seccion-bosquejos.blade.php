{{-- Seccion 5: Bosquejos --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-image me-2 text-primary"></i>Bosquejos ({{ $orden->bosquejos->count() }})</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2">
        @if($orden->bosquejos->count() > 0)
            <div class="d-flex flex-wrap gap-3">
                @foreach($orden->bosquejos as $bosquejo)
                    <div class="text-center">
                        <img src="{{ asset($bosquejo->ruta_miniatura ?? $bosquejo->ruta_archivo) }}"
                             class="bosquejo-detail-thumb border"
                             alt="{{ $bosquejo->nombre }}"
                             onclick="abrirLightbox('{{ $bosquejo->ruta_archivo }}', '{{ addslashes($bosquejo->nombre) }}')"
                             title="Click para ampliar">
                        <div class="small text-muted mt-1" style="max-width:80px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $bosquejo->nombre }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">No hay bosquejos adjuntos.</p>
        @endif
    </div>
</div>
