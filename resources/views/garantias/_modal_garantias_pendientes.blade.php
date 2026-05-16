@php
    $puedeLiberar = $puedeLiberar ?? false;
    $solicitudId = $solicitudId ?? null;
    $garantias = $garantiasPendientes ?? collect();
@endphp

<div class="modal fade" id="modalGarantiasPendientes" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-shield-exclamation"></i> Garantías pendientes</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        @if($garantias->isEmpty())
          <div class="alert alert-info mb-0">No hay garantías pendientes.</div>
        @else
          @foreach($garantias as $g)
            <div class="card mb-3" data-garantia-id="{{ $g->id }}">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="mb-0">
                    {{ $g->producto?->nombre ?? '—' }}@if($g->variante && $g->variante->nombre_variante) — {{ $g->variante->nombre_variante }}@endif
                  </h6>
                  <span class="badge bg-warning text-dark">Pendiente</span>
                </div>
                <p class="mb-1"><strong>Tipo:</strong> {{ $g->tipoLegible() }}</p>
                <p class="mb-1 text-muted small">Registrada el {{ $g->created_at?->format('d/m/Y H:i') }}</p>
                @if($g->observacion_creacion)
                  <div class="alert alert-info py-2 mb-2">
                    <strong><i class="bi bi-chat-left-text"></i> Observación de creación:</strong>
                    <div class="mt-1">{{ $g->observacion_creacion }}</div>
                  </div>
                @endif
                @if($g->estaLiberada() && $g->observacion_liberacion)
                  <div class="alert alert-success py-2 mb-2">
                    <strong><i class="bi bi-check-circle"></i> Observación de liberación:</strong>
                    <div class="mt-1">{{ $g->observacion_liberacion }}</div>
                    <div class="small text-muted mt-1">Liberada por {{ $g->usuarioLiberador?->name ?? '—' }} el {{ $g->liberado_en?->format('d/m/Y H:i') }}</div>
                  </div>
                @endif
                <div class="mt-2">
                  <strong class="d-block mb-1">Documentos:</strong>
                  @if($g->documentos->isEmpty())
                    <span class="text-muted">Sin documentos</span>
                  @else
                    @foreach($g->documentos as $doc)
                      <a href="{{ asset($doc->ruta_relativa) }}" class="btn btn-sm btn-outline-primary me-1 mb-1" target="_blank" download="{{ $doc->nombre_original }}">
                        <i class="bi bi-download"></i> {{ $doc->nombre_original }}
                      </a>
                    @endforeach
                  @endif
                </div>
                @if($puedeLiberar)
                  <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-success btn-liberar-garantia"
                            data-garantia-id="{{ $g->id }}"
                            data-solicitud-id="{{ $solicitudId }}">
                      <i class="bi bi-unlock"></i> Liberar garantía
                    </button>
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>

@if($puedeLiberar)
<div class="modal fade" id="modalLiberarGarantiaCotizacion" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Liberar garantía</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="liberar-garantia-cotizacion-id">
        <input type="hidden" id="liberar-solicitud-id">
        <div class="mb-3">
          <label class="form-label">Observación de liberación <span class="text-danger">*</span></label>
          <textarea id="liberar-garantia-observacion" class="form-control" rows="4" placeholder="Describe el motivo o resultado..."></textarea>
          <small class="text-muted">Mínimo 5 caracteres.</small>
        </div>

        @include('garantias._productos_cambio', ['prefix' => 'cotizacion', 'ubicaciones' => $ubicaciones ?? collect()])
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btnConfirmarLiberarGarantiaCotizacion"><i class="bi bi-unlock"></i> Liberar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('js/garantias/productos-cambio.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  let garProdCotizacion = null;
  if (window.GarantiaProductosCambio) {
    garProdCotizacion = window.GarantiaProductosCambio('cotizacion');
    garProdCotizacion.init();
  }

  document.querySelectorAll('.btn-liberar-garantia').forEach(btn => {
    btn.addEventListener('click', function() {
      document.getElementById('liberar-garantia-cotizacion-id').value = this.dataset.garantiaId;
      document.getElementById('liberar-solicitud-id').value = this.dataset.solicitudId || '';
      document.getElementById('liberar-garantia-observacion').value = '';
      if (garProdCotizacion) garProdCotizacion.reset();
      const modalPendientes = bootstrap.Modal.getInstance(document.getElementById('modalGarantiasPendientes'));
      if (modalPendientes) modalPendientes.hide();
      new bootstrap.Modal(document.getElementById('modalLiberarGarantiaCotizacion')).show();
    });
  });

  document.getElementById('btnConfirmarLiberarGarantiaCotizacion').addEventListener('click', function() {
    const garantiaId = document.getElementById('liberar-garantia-cotizacion-id').value;
    const solicitudId = document.getElementById('liberar-solicitud-id').value;
    const observacion = document.getElementById('liberar-garantia-observacion').value.trim();
    if (observacion.length < 5) {
      Swal.fire('Observación requerida', 'Debes ingresar una observación de al menos 5 caracteres.', 'warning');
      return;
    }
    if (garProdCotizacion) {
      const val = garProdCotizacion.validate();
      if (!val.ok) {
        Swal.fire('Datos incompletos', val.error, 'warning');
        return;
      }
    }
    const body = { observacion_liberacion: observacion };
    if (solicitudId) body.solicitud_cotizacion_id = solicitudId;
    if (garProdCotizacion) Object.assign(body, garProdCotizacion.getPayload());

    fetch(`/garantias/${garantiaId}/liberar`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(body)
    })
    .then(async res => {
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || data.message || 'Error al liberar');
      return data;
    })
    .then(data => {
      Swal.fire({
        icon: 'success',
        title: 'Garantía liberada',
        text: data.mensaje,
        confirmButtonText: 'OK'
      }).then(() => window.location.reload());
    })
    .catch(err => Swal.fire('Error', err.message, 'error'));
  });
});
</script>
@endpush
@endif
