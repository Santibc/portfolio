{{-- stock/historial.blade.php --}}
<div class="table-responsive">
  @if(!empty($ubicacion))
    <div class="alert alert-info py-2 d-flex align-items-center gap-2">
      <i class="bi bi-geo-alt-fill"></i>
      <span>Mostrando entradas y salidas de la ubicación <strong>{{ $ubicacion->nombre }}</strong></span>
    </div>
  @endif
  @if($movimientos->isEmpty())
    <p class="text-center text-muted">
      @if(!empty($ubicacion))
        No hay movimientos registrados en esta ubicación para este producto.
      @else
        No hay movimientos registrados para este producto.
      @endif
    </p>
  @else
    <table class="table table-striped table-sm">
      <thead>
        <tr>
          <th>Fecha/Hora</th>
          <th>Tipo</th>
          <th>Cantidad</th>
          <th>Stock Anterior</th>
          <th>Stock Nuevo</th>
          <th>Ubicación</th>
          <th>Origen</th>
          <th>Referencia</th>
          <th>Motivo</th>
          <th>Usuario</th>
          <th>Nota</th>
        </tr>
      </thead>
      <tbody>
        @foreach($movimientos as $movimiento)
          <tr>
            <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
            <td>
              <span class="badge bg-{{ $movimiento->color_movimiento }}">
                <i class="{{ $movimiento->icono_movimiento }}"></i>
                {{ ucfirst($movimiento->tipo_movimiento) }}
              </span>
            </td>
            <td>
              @if($movimiento->tipo_movimiento == 'salida')
                <span class="text-danger">-{{ $movimiento->cantidad }}</span>
              @elseif($movimiento->tipo_movimiento == 'entrada')
                <span class="text-success">+{{ $movimiento->cantidad }}</span>
              @elseif($movimiento->tipo_movimiento == 'ajuste')
                <span class="text-warning">
                  @if($movimiento->cantidad > 0)
                    +{{ $movimiento->cantidad }}
                  @else
                    {{ $movimiento->cantidad }}
                  @endif
                </span>
              @else
                {{ $movimiento->cantidad }}
              @endif
            </td>
            <td>{{ $movimiento->stock_anterior }}</td>
            <td>
              <strong>{{ $movimiento->stock_nuevo }}</strong>
            </td>
            <td>
              @if($movimiento->ubicacion)
                <span class="badge bg-light text-dark border">{{ $movimiento->ubicacion->nombre }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>{{ $movimiento->descripcion_origen }}</td>
            <td>
              @if($movimiento->referencia_documento)
                <code>{{ $movimiento->referencia_documento }}</code>
              @else
                -
              @endif
            </td>
            <td>
              @if($movimiento->motivo)
                <small>{{ Str::limit($movimiento->motivo, 50) }}</small>
              @else
                -
              @endif
            </td>
            <td>{{ $movimiento->usuario->name }}</td>
            <td>
              <a href="{{ route('stock.movimiento-pdf', $movimiento->id) }}"
                 class="btn btn-sm btn-outline-primary"
                 title="Descargar nota PDF"
                 target="_blank">
                <i class="bi bi-file-earmark-pdf"></i>
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    
    <div class="text-center mt-3">
      <small class="text-muted">Mostrando los últimos 50 movimientos</small>
    </div>
  @endif
</div>