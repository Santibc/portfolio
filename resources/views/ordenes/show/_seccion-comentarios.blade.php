{{-- Seccion 10: Comentarios --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-chat-dots me-2 text-primary"></i>Comentarios ({{ $orden->comentarios->count() }})</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2">
        {{-- Lista de comentarios --}}
        <div id="listaComentarios">
            @forelse($orden->comentarios->sortByDesc('created_at') as $comentario)
                <div class="comment-item">
                    <div class="d-flex justify-content-between">
                        <span class="comment-author">{{ $comentario->usuario->name ?? '-' }}</span>
                        <span class="comment-date">{{ $comentario->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="comment-content">{{ $comentario->contenido }}</div>
                </div>
            @empty
                <p class="text-muted mb-0 small" id="sinComentarios">No hay comentarios.</p>
            @endforelse
        </div>

        {{-- Formulario agregar comentario --}}
        @hasanyrole('Administrador|Recepcion')
        <div class="mt-3 pt-2 border-top">
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" id="nuevoComentario" placeholder="Escribir un comentario..." maxlength="2000">
                <button class="btn btn-sm btn-primary" type="button" id="btnAgregarComentario">
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </div>
        @endhasanyrole
    </div>
</div>
