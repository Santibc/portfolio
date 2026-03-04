{{-- Labels Management Modal --}}
<div class="modal fade" id="etiquetasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-tags me-2"></i>Etiquetas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="etiquetasLista">
                    @foreach($tablero->etiquetas as $etiqueta)
                    <div class="d-flex align-items-center gap-2 mb-2" data-etiqueta-id="{{ $etiqueta->id }}">
                        <div class="etiqueta-color-preview flex-grow-1" style="background: {{ $etiqueta->color }};">
                            {{ $etiqueta->nombre }}
                        </div>
                        @if($puedeEditar)
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarEtiqueta({{ $etiqueta->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if($puedeEditar)
                <hr>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" id="nuevaEtiquetaNombre" placeholder="Nombre...">
                    <input type="color" class="form-control form-control-sm form-control-color" id="nuevaEtiquetaColor" value="#3b82f6" style="width:40px;">
                    <button class="btn btn-primary btn-sm" onclick="crearEtiqueta()">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
