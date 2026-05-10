@php
    $items = $solicitud->items;
@endphp

<form id="formEditarCotizacion" data-solicitud-id="{{ $solicitud->id }}"
      data-puede-editar-precios="{{ $puedeEditarPrecios ? '1' : '0' }}">
    @csrf

    <div class="row mb-3">
        <div class="col-md-6">
            <h6>Cliente</h6>
            <div><strong>{{ $solicitud->cliente->nombre_contacto }}</strong></div>
            <div class="text-muted small">
                Lista de precio: {{ $solicitud->cliente->listaPrecio?->nombre ?? 'Sin lista' }}
            </div>
        </div>
        <div class="col-md-6">
            <h6>Cotización</h6>
            <div><code>{{ $solicitud->numero_solicitud }}</code></div>
            <div class="text-muted small">{{ $solicitud->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Notas del cliente</label>
        <textarea name="notas_cliente" class="form-control" rows="2">{{ $solicitud->notas_cliente }}</textarea>
    </div>

    @if($puedeEditarPrecios)
    <div class="mb-3">
        <label class="form-label">Observaciones internas (admin)</label>
        <textarea name="observaciones_admin" class="form-control" rows="2">{{ $solicitud->observaciones_admin }}</textarea>
    </div>
    @endif

    <h6>Productos</h6>
    <div class="table-responsive">
        <table class="table table-sm align-middle" id="tablaItemsEditar">
            <thead class="table-light">
                <tr>
                    <th>Referencia</th>
                    <th>Producto</th>
                    <th>Variante</th>
                    <th style="width:110px">Cantidad</th>
                    <th style="width:140px">Precio unit.</th>
                    <th style="width:130px">Subtotal</th>
                    <th style="width:60px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr data-item-id="{{ $item->id }}">
                    <td><code>{{ $item->referencia_producto }}</code></td>
                    <td>{{ $item->nombre_producto }}</td>
                    <td>{{ $item->info_variante ?: '-' }}</td>
                    <td>
                        <input type="number" min="1" name="items[{{ $item->id }}][cantidad]"
                               value="{{ $item->cantidad }}" class="form-control form-control-sm item-cantidad">
                        <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][precio_unitario]"
                               value="{{ number_format($item->precio_unitario, 2, '.', '') }}"
                               class="form-control form-control-sm item-precio"
                               {{ $puedeEditarPrecios ? '' : 'readonly' }}>
                    </td>
                    <td class="item-subtotal">${{ number_format($item->precio_total, 2) }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-item"
                                data-item-id="{{ $item->id }}" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Total:</th>
                    <th id="editarMontoTotal">${{ number_format($solicitud->monto_total, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <hr>

    <h6>Agregar producto</h6>
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small mb-1">Buscar (referencia o nombre)</label>
            <input type="text" id="buscarProductoInput" class="form-control"
                   placeholder="Escribe al menos 2 caracteres..." autocomplete="off">
            <div id="buscarProductoResultados" class="list-group position-absolute w-50"
                 style="z-index:1080; max-height:240px; overflow-y:auto;"></div>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Cantidad</label>
            <input type="number" id="nuevoCantidad" class="form-control" min="1" value="1">
        </div>
        @if($puedeEditarPrecios)
        <div class="col-md-2">
            <label class="form-label small mb-1">Precio (opcional)</label>
            <input type="number" step="0.01" min="0" id="nuevoPrecio" class="form-control" placeholder="Auto">
        </div>
        @endif
        <div class="col-md-2">
            <button type="button" class="btn btn-success w-100" id="btnAgregarItem" disabled>
                <i class="bi bi-plus-circle"></i> Agregar
            </button>
        </div>
    </div>
    <small id="nuevoPrecioHint" class="text-muted d-block mt-1">
        Selecciona un producto para ver su precio según la lista del cliente.
    </small>
    <input type="hidden" id="nuevoProductoId">
    <input type="hidden" id="nuevoVarianteId">

    <hr>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarEdicion">
            <i class="bi bi-save"></i> Guardar cambios
        </button>
    </div>
</form>
