{{-- Cabecera del modal con datos del producto/variante --}}
<div class="mb-3 pb-3 border-bottom">
  <h5 class="mb-1">
    <strong>{{ $producto->referencia }}</strong> — {{ $producto->nombre }}
  </h5>
  @if($variante)
    <div><small class="text-muted">Variante: <strong>{{ $variante->nombre_variante }}</strong></small></div>
  @endif
  @php
    $totalDisponible = $stocks->sum('cantidad_disponible');
    $totalReservado = $stocks->sum('cantidad_reservada');
    $totalReal = $totalDisponible - $totalReservado;
  @endphp
  <div class="mt-2 d-flex gap-3 flex-wrap">
    <span class="badge bg-success" style="font-size: 0.9rem;">Total disponible: {{ $totalDisponible }}</span>
    <span class="badge bg-warning text-dark" style="font-size: 0.9rem;">Total reservado: {{ $totalReservado }}</span>
    <span class="badge bg-primary" style="font-size: 0.9rem;">Stock real total: {{ $totalReal }}</span>
  </div>
</div>

{{-- Tabla de stock por ubicación --}}
@if($stocks->isEmpty())
  <div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    Este producto aún no tiene stock registrado en ninguna ubicación.
  </div>
@else
  <div class="table-responsive">
    <table class="table table-sm table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Ubicación</th>
          <th class="text-center">Disp.</th>
          <th class="text-center">Reserv.</th>
          <th class="text-center">Stock Real</th>
          <th class="text-center">Mín/Máx</th>
          <th>Ubic. específica</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($stocks as $s)
          @php
            $stockReal = $s->cantidad_disponible - $s->cantidad_reservada;
            $stockBajo = $s->alerta_stock_bajo && $stockReal <= $s->stock_minimo;
            if ($stockReal <= 0) {
              $badge = 'danger';
            } elseif ($stockBajo) {
              $badge = 'warning';
            } else {
              $badge = 'success';
            }
            $tipoUbi = $s->ubicacionRelacion?->tipo;
            $tipoColor = match($tipoUbi) {
              'bodega' => 'bg-primary',
              'tienda' => 'bg-info',
              default => 'bg-secondary',
            };
          @endphp
          <tr>
            <td>
              @if($s->ubicacionRelacion)
                <strong>{{ $s->ubicacionRelacion->nombre }}</strong>
                <span class="badge {{ $tipoColor }} ms-1">{{ ucfirst($tipoUbi ?? '-') }}</span>
              @else
                <span class="text-muted">Sin ubicación</span>
              @endif
            </td>
            <td class="text-center">{{ $s->cantidad_disponible }}</td>
            <td class="text-center">{{ $s->cantidad_reservada }}</td>
            <td class="text-center"><span class="badge bg-{{ $badge }}">{{ $stockReal }}</span></td>
            <td class="text-center">
              {{ $s->stock_minimo }} / {{ $s->stock_maximo ?? '∞' }}
            </td>
            <td>
              <small class="text-muted">{{ $s->ubicacion ?: '—' }}</small>
            </td>
            <td class="text-end">
              <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary"
                        onclick="verHistorialUbicacion({{ $producto->id }}, {{ $variante?->id ?? 'null' }}, {{ $s->ubicacion_id ?? 'null' }}, @js($s->ubicacionRelacion?->nombre ?? 'Sin ubicación'))"
                        title="Ver historial de entradas y salidas en esta ubicación">
                  <i class="bi bi-clock-history"></i>
                </button>
                @hasanyrole('admin|auxiliar_administrativo|inventarios|auxiliar_inventario')
                  <button type="button" class="btn btn-success" onclick="entradaStock({{ $s->id }})" title="Entrada">
                    <i class="bi bi-plus-circle"></i>
                  </button>
                  <button type="button" class="btn btn-danger" onclick="salidaStock({{ $s->id }})" title="Salida">
                    <i class="bi bi-dash-circle"></i>
                  </button>
                @endhasanyrole
                @hasanyrole('admin|auxiliar_administrativo|inventarios')
                  <button type="button" class="btn btn-warning" onclick="ajusteStock({{ $s->id }})" title="Ajuste">
                    <i class="bi bi-gear"></i>
                  </button>
                  <button type="button" class="btn btn-info" onclick="configurarStock({{ $s->id }})" title="Configurar">
                    <i class="bi bi-sliders"></i>
                  </button>
                @endhasanyrole
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif

{{-- Agregar producto en otra ubicación --}}
@hasanyrole('admin|auxiliar_administrativo|inventarios')
  @if($ubicacionesDisponibles->isNotEmpty())
    <div class="mt-3 pt-3 border-top">
      <label class="form-label fw-bold">
        <i class="bi bi-plus-square"></i> Agregar en otra ubicación:
      </label>
      <div class="d-flex gap-2">
        <select id="ubicacion-nueva-select" class="form-select">
          @foreach($ubicacionesDisponibles as $u)
            <option value="{{ $u->id }}">{{ $u->nombre }} ({{ ucfirst($u->tipo) }})</option>
          @endforeach
        </select>
        <button type="button" class="btn btn-primary"
                onclick="agregarUbicacionAlProducto({{ $producto->id }}, {{ $variante?->id ?? 'null' }})">
          <i class="bi bi-plus-circle"></i> Agregar
        </button>
      </div>
      <small class="text-muted d-block mt-1">
        Se creará un registro con stock 0 en la ubicación seleccionada. Luego podrás registrar una entrada.
      </small>
    </div>
  @endif
@endhasanyrole
