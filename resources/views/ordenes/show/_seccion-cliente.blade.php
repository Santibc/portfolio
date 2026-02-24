{{-- Seccion 2: Informacion del Cliente --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2 text-primary"></i>Cliente</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2">
        @if($orden->cliente)
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>{{ $orden->cliente->nombre }}</strong></p>
                    @if($orden->cliente->celular_1)
                        <p class="mb-1 text-muted small"><i class="bi bi-phone me-1"></i>{{ $orden->cliente->celular_1 }}</p>
                    @endif
                    @if($orden->cliente->celular_2)
                        <p class="mb-1 text-muted small"><i class="bi bi-phone me-1"></i>{{ $orden->cliente->celular_2 }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    @if($orden->cliente->correo)
                        <p class="mb-1 text-muted small"><i class="bi bi-envelope me-1"></i>{{ $orden->cliente->correo }}</p>
                    @endif
                    @if($orden->cliente->direccion)
                        <p class="mb-1 text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $orden->cliente->direccion }}</p>
                    @endif
                    <a href="{{ route('recepcion.clientes.show', $orden->cliente) }}" class="small text-primary">Ver detalle del cliente</a>
                </div>
            </div>
        @else
            <p class="text-muted mb-0">Cliente no asignado</p>
        @endif
    </div>
</div>
