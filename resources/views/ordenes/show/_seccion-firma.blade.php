{{-- Seccion 8: Firma del Cliente --}}
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white border-0 px-4 pt-3 pb-0">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-pen me-2 text-primary"></i>Firma del Cliente</h6>
    </div>
    <div class="card-body px-4 pb-3 pt-2 text-center">
        @if($orden->ruta_firma_cliente)
            <img src="{{ asset($orden->ruta_firma_cliente) }}" class="img-fluid border rounded" style="max-height:150px;" alt="Firma del cliente">
        @else
            <p class="text-muted mb-0 small">Sin firma registrada.</p>
        @endif
    </div>
</div>
