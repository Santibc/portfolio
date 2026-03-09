{{-- Seccion: Piezas (con bosquejos integrados) --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-puzzle me-2 text-primary"></i>Piezas ({{ $orden->piezas->count() }})</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2">
        @if($orden->piezas->count() > 0)
            @foreach($orden->piezas as $pieza)
                <div class="pieza-card">
                    <div class="d-flex gap-3">
                        {{-- Bosquejo thumbnail --}}
                        @if($pieza->bosquejo)
                            <div class="flex-shrink-0 text-center">
                                <img src="{{ asset($pieza->bosquejo->ruta_miniatura ?? $pieza->bosquejo->ruta_archivo) }}"
                                     class="border rounded"
                                     alt="{{ $pieza->bosquejo->nombre }}"
                                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                     onclick="abrirLightbox('{{ $pieza->bosquejo->ruta_archivo }}', '{{ addslashes($pieza->bosquejo->nombre) }}')"
                                     title="{{ $pieza->bosquejo->nombre }} - Click para ampliar">
                                <div class="small text-muted mt-1" style="max-width:80px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $pieza->bosquejo->nombre }}">
                                    {{ $pieza->bosquejo->nombre }}
                                </div>
                            </div>
                        @endif

                        {{-- Pieza info --}}
                        <div class="flex-grow-1 min-w-0">
                            <div class="pieza-header">
                                <div>
                                    <strong>{{ $pieza->nombre }}</strong>
                                    <span class="text-muted ms-2 small">x{{ $pieza->cantidad }}</span>
                                    @if($pieza->cantidad_entregada >= $pieza->cantidad)
                                        <span class="status-badge success ms-2">ENTREGADA</span>
                                    @elseif($pieza->cantidad_entregada > 0)
                                        <span class="status-badge info ms-2">ENTREGADA {{ $pieza->cantidad_entregada }}/{{ $pieza->cantidad }}</span>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold {{ $pieza->porcentaje_avance >= 100 ? 'text-success' : ($pieza->porcentaje_avance > 0 ? 'text-warning' : 'text-muted') }}">
                                        {{ number_format($pieza->porcentaje_avance, 0) }}%
                                    </span>
                                </div>
                            </div>

                            @if($pieza->especificacion)
                                <div class="pieza-spec mb-2">{{ $pieza->especificacion }}</div>
                            @endif

                            @if($pieza->notas)
                                <div class="small text-muted fst-italic mb-2">
                                    <i class="bi bi-sticky me-1"></i>{{ $pieza->notas }}
                                </div>
                            @endif

                            {{-- Progress bar de avance --}}
                            @php
                                $pct = min(100, max(0, $pieza->porcentaje_avance));
                                $barColor = $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : ($pct > 0 ? 'bg-info' : 'bg-secondary'));
                            @endphp
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $barColor }}" style="width: {{ $pct }}%"></div>
                            </div>

                            {{-- Progress bar de entrega --}}
                            @if($pieza->cantidad > 0)
                                @php
                                    $pctEntrega = min(100, ($pieza->cantidad_entregada / $pieza->cantidad) * 100);
                                @endphp
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <div class="progress flex-grow-1" style="height: 5px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $pctEntrega }}%"></div>
                                    </div>
                                    <small class="text-muted" style="white-space:nowrap;">{{ $pieza->cantidad_entregada }}/{{ $pieza->cantidad }} entregadas</small>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">
                                    @if($pieza->material || $pieza->calibre)
                                        {{ $pieza->material ?? '' }} {{ $pieza->calibre ?? '' }}
                                    @endif
                                </small>
                                <small class="text-muted">
                                    @if(!$pieza->requiere_operario)
                                        <x-sinden.badge variant="secondary">Sin operario</x-sinden.badge>
                                    @elseif($pieza->operarioActual)
                                        <i class="bi bi-person me-1"></i>{{ $pieza->operarioActual->name }}
                                    @else
                                        <span class="fst-italic">Sin operario asignado</span>
                                    @endif
                                </small>
                            </div>

                            <div class="mt-2 d-flex gap-3">
                                {{-- Historial de avances (colapsable) --}}
                                @if($pieza->historialAvances->count() > 0)
                                    <a class="small text-primary" data-bs-toggle="collapse" href="#historial{{ $pieza->id }}">
                                        <i class="bi bi-clock-history me-1"></i>Ver avances ({{ $pieza->historialAvances->count() }})
                                    </a>
                                @endif

                                {{-- Boton historial de entregas --}}
                                @if($pieza->cantidad_entregada > 0)
                                    <a href="#" class="small text-info btn-historial-entregas" data-pieza-id="{{ $pieza->id }}" data-pieza-nombre="{{ $pieza->nombre }}">
                                        <i class="bi bi-box-arrow-right me-1"></i>Ver entregas
                                    </a>
                                @endif
                            </div>

                            {{-- Historial de avances colapsable --}}
                            @if($pieza->historialAvances->count() > 0)
                                <div class="collapse mt-2" id="historial{{ $pieza->id }}">
                                    <div class="historial-timeline">
                                        @foreach($pieza->historialAvances->sortByDesc('created_at') as $avance)
                                            <div class="historial-entry">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-medium small">{{ $avance->operario->name ?? '-' }}</span>
                                                    <span class="text-muted small">{{ $avance->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <span class="small">
                                                    {{ number_format($avance->porcentaje_desde, 0) }}% &rarr; {{ number_format($avance->porcentaje_hasta, 0) }}%
                                                    <span class="text-muted">(+{{ number_format($avance->contribucion, 0) }}%)</span>
                                                </span>
                                                @if($avance->notas)
                                                    <div class="text-muted small fst-italic">{{ $avance->notas }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted mb-0">No hay piezas definidas (venta directa).</p>
        @endif

        {{-- Bosquejos sin pieza vinculada --}}
        @php
            $bosquejoIdsVinculados = $orden->piezas->pluck('orden_bosquejo_id')->filter()->toArray();
            $bosquejosSinPieza = $orden->bosquejos->whereNotIn('id', $bosquejoIdsVinculados);
        @endphp
        @if($bosquejosSinPieza->count() > 0)
            <div class="{{ $orden->piezas->count() > 0 ? 'mt-3 pt-3 border-top' : '' }}">
                <h6 class="fw-semibold text-muted small mb-2">
                    <i class="bi bi-image me-1"></i>Bosquejos sin pieza vinculada ({{ $bosquejosSinPieza->count() }})
                </h6>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($bosquejosSinPieza as $bosquejo)
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
            </div>
        @endif
    </div>
</div>

{{-- Modal Historial de Entregas por Pieza --}}
<div class="modal fade" id="modalHistorialEntregas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-box-arrow-right me-2 text-primary"></i>Historial de Entregas - <span id="modalEntregaPiezaNombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="d-flex gap-3">
                        <span class="badge bg-light text-dark border" id="modalEntregaResumen"></span>
                    </div>
                </div>
                <div id="modalEntregaLoading" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <p class="mt-1 mb-0 text-muted small">Cargando historial...</p>
                </div>
                <div id="modalEntregaContenido" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Entregado Por</th>
                                    <th>Foto</th>
                                </tr>
                            </thead>
                            <tbody id="modalEntregaTabla"></tbody>
                        </table>
                    </div>
                </div>
                <div id="modalEntregaVacio" class="d-none text-center text-muted py-3">
                    No hay entregas registradas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
