<div class="mb-3">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <small class="text-muted d-block">Producto / Variante</small>
      <strong>{{ $titulo }}</strong>
    </div>
    <div class="text-end">
      <small class="text-muted d-block">Código actual</small>
      <span class="badge bg-dark fs-6">
        {{ $codigoActual ?? 'No asignado' }}
      </span>
    </div>
  </div>
</div>

<hr>

@if($logs->isEmpty())
  <div class="alert alert-secondary text-center mb-0">
    <i class="bi bi-info-circle"></i> Sin cambios registrados en el código de barras.
  </div>
@else
  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>Fecha</th>
          <th>Usuario</th>
          <th>Código anterior</th>
          <th>Código nuevo</th>
          <th>Origen</th>
        </tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
          <tr>
            <td>
              <span title="{{ $log->created_at }}">
                {{ $log->created_at->format('d/m/Y H:i') }}
              </span>
            </td>
            <td>
              {{ $log->usuario?->name ?? '—' }}
            </td>
            <td>
              @if($log->codigo_anterior)
                <code>{{ $log->codigo_anterior }}</code>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($log->codigo_nuevo)
                <code>{{ $log->codigo_nuevo }}</code>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($log->origen === 'modal_escaneo')
                <span class="badge bg-info">Escaneo</span>
              @elseif($log->origen === 'modal_eliminacion')
                <span class="badge bg-danger">Eliminación</span>
              @elseif($log->origen === 'formulario')
                <span class="badge bg-primary">Formulario</span>
              @else
                <span class="badge bg-secondary">{{ $log->origen }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <small class="text-muted">Total de cambios: {{ $logs->count() }}</small>
@endif
