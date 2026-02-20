{{-- stock/reservas.blade.php --}}
<div class="table-responsive">
  @if($reservas->isEmpty())
    <p class="text-center text-muted">No hay reservas registradas para este producto.</p>
  @else
    <table class="table table-striped table-sm">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Cotizacion</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Estado</th>
          <th>Expira</th>
          <th>Creada</th>
        </tr>
      </thead>
      <tbody>
        @foreach($reservas as $reserva)
          <tr>
            <td>
              @if($reserva->solicitudCotizacion && $reserva->solicitudCotizacion->cliente)
                {{ $reserva->solicitudCotizacion->cliente->razon_social ?: $reserva->solicitudCotizacion->cliente->nombre_contacto }}
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              @if($reserva->solicitudCotizacion)
                <code>{{ $reserva->solicitudCotizacion->numero_solicitud }}</code>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              @if($reserva->itemSolicitud)
                {{ $reserva->itemSolicitud->nombre_producto }}
                @if($reserva->itemSolicitud->info_variante)
                  <br><small class="text-muted">{{ $reserva->itemSolicitud->info_variante }}</small>
                @endif
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

    <div class="text-center mt-3">
      <small class="text-muted">Mostrando las ultimas 50 reservas</small>
    </div>
  @endif
</div>
