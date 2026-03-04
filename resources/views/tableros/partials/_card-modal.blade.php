{{-- Card Detail Modal --}}
<div class="modal fade modal-tarjeta" id="tarjetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-card-heading me-2 mt-1" style="color:#5e6c84;font-size:1.2rem;"></i>
                        <div class="flex-grow-1">
                            <input type="text" class="tarjeta-modal-titulo" id="modalTitulo"
                                   {{ $puedeEditar ? '' : 'readonly' }}>
                            <div class="tarjeta-modal-subtitulo">
                                en la lista <strong id="modalColumnaNombre"></strong>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Left column: Content --}}
                    <div class="col-md-8">
                        {{-- Labels --}}
                        <div class="tarjeta-seccion" id="seccionEtiquetas">
                            <div class="tarjeta-seccion-titulo">
                                <i class="bi bi-tags"></i> Etiquetas
                            </div>
                            <div class="d-flex flex-wrap gap-1" id="modalEtiquetas"></div>
                        </div>

                        {{-- Members --}}
                        <div class="tarjeta-seccion" id="seccionMiembros">
                            <div class="tarjeta-seccion-titulo">
                                <i class="bi bi-people"></i> Miembros
                            </div>
                            <div class="d-flex flex-wrap gap-1" id="modalMiembros"></div>
                        </div>

                        {{-- Due date & Priority --}}
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="tarjeta-seccion-titulo">
                                    <i class="bi bi-calendar-event"></i> Vencimiento
                                </div>
                                <div id="modalFechaDisplay" class="small"></div>
                            </div>
                            <div class="col-6">
                                <div class="tarjeta-seccion-titulo">
                                    <i class="bi bi-flag"></i> Prioridad
                                </div>
                                <div id="modalPrioridadDisplay" class="small"></div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="tarjeta-seccion">
                            <div class="tarjeta-seccion-titulo">
                                <i class="bi bi-text-left"></i> Descripcion
                            </div>
                            <textarea class="tarjeta-descripcion-editor" id="modalDescripcion"
                                      placeholder="Agregar una descripcion mas detallada..."
                                      {{ $puedeEditar ? '' : 'readonly' }}></textarea>
                            @if($puedeEditar)
                            <button class="btn btn-primary btn-sm mt-2" id="btnGuardarDescripcion" style="display:none;"
                                    onclick="guardarDescripcion()">Guardar</button>
                            @endif
                        </div>

                        {{-- Checklists --}}
                        <div id="checklistsContainer"></div>

                        {{-- Attachments --}}
                        <div class="tarjeta-seccion" id="seccionAdjuntos" style="display:none;">
                            <div class="tarjeta-seccion-titulo">
                                <i class="bi bi-paperclip"></i> Adjuntos
                            </div>
                            <div id="modalAdjuntos"></div>
                        </div>

                        {{-- Activity / Comments --}}
                        <div class="tarjeta-seccion">
                            <div class="tarjeta-seccion-titulo">
                                <i class="bi bi-chat-left-text"></i> Actividad
                            </div>
                            @can('comentar_tarjetas')
                            <div class="d-flex gap-2 mb-3">
                                <div class="comentario-avatar">{{ auth()->user()->initials }}</div>
                                <div class="flex-grow-1">
                                    <textarea class="form-control form-control-sm" id="nuevoComentario"
                                              rows="2" placeholder="Escribe un comentario..."></textarea>
                                    <button class="btn btn-primary btn-sm mt-1" onclick="agregarComentario()">Comentar</button>
                                </div>
                            </div>
                            @endcan
                            <div id="modalComentarios"></div>
                        </div>
                    </div>

                    {{-- Right column: Actions --}}
                    <div class="col-md-4">
                        @if($puedeEditar)
                        <div class="small text-muted fw-semibold mb-2">Agregar a la tarjeta</div>
                        <div class="tarjeta-acciones">
                            <button class="btn-accion-tarjeta" onclick="toggleMiembrosPicker()">
                                <i class="bi bi-person"></i> Miembros
                            </button>
                            <button class="btn-accion-tarjeta" onclick="toggleEtiquetasPicker()">
                                <i class="bi bi-tag"></i> Etiquetas
                            </button>
                            <button class="btn-accion-tarjeta" onclick="agregarChecklist()">
                                <i class="bi bi-check2-square"></i> Checklist
                            </button>
                            <button class="btn-accion-tarjeta" onclick="toggleFechaPicker()">
                                <i class="bi bi-calendar-event"></i> Fecha
                            </button>
                            <button class="btn-accion-tarjeta" onclick="document.getElementById('archivoInput').click()">
                                <i class="bi bi-paperclip"></i> Adjunto
                            </button>
                            <input type="file" id="archivoInput" style="display:none" onchange="subirAdjunto(this)">
                            <button class="btn-accion-tarjeta" onclick="toggleColorPicker()">
                                <i class="bi bi-palette"></i> Portada
                            </button>

                            {{-- Inline pickers (hidden by default) --}}
                            <div id="miembrosPicker" class="mt-2" style="display:none;">
                                <div class="card card-body p-2" id="miembrosPickerList"></div>
                            </div>
                            <div id="etiquetasPicker" class="mt-2" style="display:none;">
                                <div class="card card-body p-2" id="etiquetasPickerList"></div>
                            </div>
                            <div id="fechaPicker" class="mt-2" style="display:none;">
                                <div class="card card-body p-2">
                                    <input type="datetime-local" class="form-control form-control-sm" id="fechaVencimientoInput">
                                    <div class="d-flex gap-1 mt-2">
                                        <button class="btn btn-primary btn-sm flex-grow-1" onclick="guardarFecha()">Guardar</button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="eliminarFecha()">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="colorPicker" class="mt-2" style="display:none;">
                                <div class="card card-body p-2">
                                    <div class="color-grid">
                                        @foreach(['#ef4444','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899','#06b6d4','#84cc16','#f97316','#6b7280'] as $c)
                                        <div class="color-option" style="background:{{ $c }}" data-color="{{ $c }}"
                                             onclick="guardarColorPortada('{{ $c }}', this)"></div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-outline-secondary btn-sm mt-2 w-100" onclick="guardarColorPortada(null)">
                                        <i class="bi bi-x-lg"></i> Quitar portada
                                    </button>
                                </div>
                            </div>

                            <hr class="my-2">
                            <div class="small text-muted fw-semibold mb-2">Acciones</div>
                            <button class="btn-accion-tarjeta" onclick="cambiarPrioridad()">
                                <i class="bi bi-flag"></i> Prioridad
                            </button>
                            <button class="btn-accion-tarjeta" onclick="archivarTarjeta()">
                                <i class="bi bi-archive"></i> Archivar
                            </button>
                            @can('eliminar_tableros')
                            <button class="btn-accion-tarjeta danger" onclick="eliminarTarjeta()">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                            @endcan
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
