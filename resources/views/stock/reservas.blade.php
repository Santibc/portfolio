{{-- stock/reservas.blade.php --}}
<div>
  {{-- Seccion 1: Cotizaciones con reservas activas --}}
  @if($cotizacionesActivas->isNotEmpty())
    <h6 class="text-primary mb-3"><i class="bi bi-bookmark-check"></i> Cotizaciones con Reserva Activa ({{ $cotizacionesActivas->count() }})</h6>
    <div class="table-responsive mb-4">
      <table class="table table-sm table-bordered">
        <thead class="table-primary">
          <tr>
            <th>Cotizacion</th>
            <th>Cliente</th>
            <th>Estado Cotizacion</th>
            <th>Cantidad Reservada</th>
            <th>Expira</th>
            <th>Creada</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cotizacionesActivas as $data)
            @php $sc = $data['solicitud']; @endphp
            <tr>
              <td>
                @if($sc)
                  <a href="{{ route('solicitudes.detalle', $sc->id) }}" target="_blank" class="text-decoration-none">
                    <code>{{ $sc->numero_solicitud }}</code>
                    <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                  </a>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($sc && $sc->cliente)
                  {{ $sc->cliente->razon_social ?: $sc->cliente->nombre_contacto }}
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($sc)
                  <span class="badge bg-{{ $sc->estado === 'pendiente' ? 'warning' : ($sc->estado === 'aplicada' ? 'success' : 'secondary') }}">
                    {{ ucfirst($sc->estado) }}
                  </span>
                @endif
              </td>
              <td><strong>{{ $data['total_reservado'] }}</strong></td>
              <td>
                @php $primeraReserva = $data['reservas']->first(); @endphp
                @if($primeraReserva && $primeraReserva->expira_en)
                  @if($primeraReserva->haExpirado())
                    <span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Expirada</span>
                  @else
                    {{ $primeraReserva->tiempo_restante }}
                  @endif
                @else
                  -
                @endif
              </td>
              <td>{{ $data['reservas']->first()->created_at->format('d/m/Y H:i') }}</td>
              <td>
                @if($sc)
                  <a href="{{ route('solicitudes.detalle', $sc->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver cotizacion">
                    <i class="bi bi-eye"></i>
                  </a>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="table-light">
            <td colspan="3" class="text-end"><strong>Total reservado:</strong></td>
            <td><strong>{{ $cotizacionesActivas->sum('total_reservado') }}</strong></td>
            <td colspan="3"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  @else
    <div class="alert alert-success mb-4">
      <i class="bi bi-check-circle"></i> No hay cotizaciones con reservas activas para este producto.
    </div>
  @endif

  {{-- Seccion 2: Historial completo de reservas --}}
  @if($reservas->isNotEmpty())
    <h6 class="text-secondary mb-3"><i class="bi bi-clock-history"></i> Historial de Reservas</h6>
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Cotizacion</th>
            <th>Cantidad</th>
            <th>Estado</th>
            <th>Expira</th>
            <th>Creada</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reservas as $reserva)
            <tr class="{{ $reserva->estado === 'activa' ? 'table-info' : '' }}">
              <td>
                @if($reserva->solicitudCotizacion && $reserva->solicitudCotizacion->cliente)
                  {{ $reserva->solicitudCotizacion->cliente->razon_social ?: $reserva->solicitudCotizacion->cliente->nombre_contacto }}
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($reserva->solicitudCotizacion)
                  <a href="{{ route('solicitudes.detalle', $reserva->solicitudCotizacion->id) }}" target="_blank" class="text-decoration-none">
                    <code>{{ $reserva->solicitudCotizacion->numero_solicitud }}</code>
                  </a>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td><strong>{{ $reserva->cantidad_reservada }}</strong></td>
              <td>
                <span class="badge bg-{{ $reserva->color_estado }}">
                  {{ $reserva->etiqueta_estado }}
                </span>
              </td>
              <td>
                @if($reserva->estado === 'activa')
                  @if($reserva->haExpirado())
                    <span class="text-danger">Expirada</span>
                  @else
                    {{ $reserva->tiempo_restante }}
                  @endif
                @elseif($reserva->expira_en)
                  {{ $reserva->expira_en->format('d/m/Y H:i') }}
                @else
                  -
                @endif
              </td>
              <td>{{ $reserva->created_at->format('d/m/Y H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="text-center mt-2">
        <small class="text-muted">Mostrando las ultimas 50 reservas</small>
      </div>
    </div>
  @endif
</div>
