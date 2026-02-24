{{-- Seccion 11: Garantias --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check me-2 text-primary"></i>Garantias ({{ $orden->garantias->count() }})</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2">
        @if($orden->garantias->count() > 0)
            @foreach($orden->garantias as $garantia)
                <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <span class="fw-medium">{{ $garantia->pieza->nombre ?? '-' }}</span>
                        <span class="text-muted small ms-1">(x{{ $garantia->cantidad_devuelta }})</span>
                        @php
                            $estadoGarantia = [
                                'abierta' => 'warning', 'en_proceso' => 'info',
                                'completada' => 'success', 'reentregada' => 'primary',
                            ];
                        @endphp
                        <span class="status-badge {{ $estadoGarantia[$garantia->estado] ?? 'secondary' }} ms-2">
                            {{ strtoupper(str_replace('_', ' ', $garantia->estado)) }}
                        </span>
                        <div class="text-muted small">{{ Str::limit($garantia->motivo, 100) }}</div>
                        @if($garantia->cobrable)
                            <span class="small text-danger">Cobrable: ${{ number_format($garantia->monto_cobro, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted mb-0 small">No hay devoluciones por garantia.</p>
        @endif
    </div>
</div>
