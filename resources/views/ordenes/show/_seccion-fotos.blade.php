{{-- Seccion 9: Galeria de Fotos (excluye fotos de entrega) --}}
@php $fotosGenerales = $orden->fotos->where('tipo_foto', '!=', 'entrega'); @endphp
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-camera me-2 text-primary"></i>Fotos de operarios ({{ $fotosGenerales->count() }})</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2">
        @if($fotosGenerales->count() > 0)
            <div class="photo-gallery">
                @foreach($fotosGenerales as $foto)
                    <div class="text-center">
                        <img src="{{ asset($foto->ruta_miniatura ?? $foto->ruta_archivo) }}"
                             alt="{{ $foto->tipo_foto }}"
                             onclick="abrirLightbox('{{ $foto->ruta_archivo }}', '{{ ucfirst($foto->tipo_foto) }}')"
                             title="{{ ucfirst($foto->tipo_foto) }} - {{ $foto->subidoPorUsuario->name ?? '' }}">
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0 small">No hay fotos adjuntas.</p>
        @endif
    </div>
</div>
