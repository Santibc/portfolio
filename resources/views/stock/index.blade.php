<x-app-layout>
  <x-slot name="header">Gestión de Stock</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      {{-- Alertas --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- Tarjetas de resumen --}}
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card border-success">
            <div class="card-body">
              <h6 class="card-title text-success">
                <i class="bi bi-check-circle"></i> Con Stock
              </h6>
              <p class="card-text display-6" id="productosConStock">-</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-warning">
            <div class="card-body">
              <h6 class="card-title text-warning">
                <i class="bi bi-exclamation-triangle"></i> Stock Bajo
              </h6>
              <p class="card-text display-6">{{ $productosConStockBajo }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-danger">
            <div class="card-body">
              <h6 class="card-title text-danger">
                <i class="bi bi-x-circle"></i> Sin Stock
              </h6>
              <p class="card-text display-6">{{ $productosSinStock }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-info">
            <div class="card-body">
              <h6 class="card-title text-info">
                <i class="bi bi-box-seam"></i> Total Items
              </h6>
              <p class="card-text display-6" id="totalItems">-</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Acciones rápidas --}}
      @if(auth()->user()->hasRole(['admin', 'auxiliar_administrativo', 'inventarios']))
      <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('traslados.form') }}" class="btn btn-primary">
          <i class="bi bi-arrow-left-right me-1"></i> Nuevo Traslado
        </a>
        <a href="{{ route('traslados') }}" class="btn btn-outline-secondary ms-2">
          <i class="bi bi-list-ul me-1"></i> Ver Traslados
        </a>
      </div>
      @endif

      {{-- Panel de Filtros --}}
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
          <h5 class="mb-0">
            <i class="bi bi-funnel"></i> Filtros
            @if(request('producto_id'))
              <span class="badge bg-info ms-2">Filtro activo</span>
            @endif
          </h5>
        </div>
        <div class="card-body">
          <div class="row align-items-end">
            <div class="col-md-4">
              <label class="form-label">Buscar Producto</label>
              <select id="filtroProducto" class="form-select select2-productos w-100">
                <option value="">-- Todos los productos --</option>
                @if($productoFiltrado ?? false)
                  <option value="{{ $productoFiltrado->id }}" selected>
                    {{ $productoFiltrado->referencia }} - {{ $productoFiltrado->nombre }}
                    @if($productoFiltrado->tiene_variantes)
                      (Con variantes)
                    @endif
                  </option>
                @endif
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Estado de Stock</label>
              <select id="filtroEstado" class="form-select">
                <option value="">-- Todos --</option>
                <option value="con_stock">Con Stock</option>
                <option value="sin_stock">Sin Stock</option>
                <option value="stock_bajo">Stock Bajo</option>
              </select>
            </div>

            <div class="col-md-3">
              <div class="btn-group w-100" role="group">
                <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                  <i class="bi bi-check-circle"></i> Aplicar
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="limpiarFiltros()">
                  <i class="bi bi-x-circle"></i> Limpiar
                </button>
              </div>
            </div>
          </div>

          {{-- Información del filtro activo --}}
          @if($productoFiltrado ?? false)
            <div class="alert alert-info mt-3 mb-0">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <strong><i class="bi bi-info-circle"></i> Mostrando stock de:</strong>
                  <span class="ms-2">{{ $productoFiltrado->referencia }} - {{ $productoFiltrado->nombre }}</span>
                  @if($productoFiltrado->tiene_variantes)
                    <span class="badge bg-secondary ms-2">
                      {{ $productoFiltrado->variantes->count() }} variantes
                    </span>
                  @endif
                </div>
                <div>
                  <a href="{{ route('productos.form', $productoFiltrado->id) }}" 
                     class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i> Editar Producto
                  </a>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- Tabla de stock --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h4 class="text-2xl font-semibold mb-4">Control de Stock</h4>

          <table id="stock-table" class="table-responsive w-full text-sm text-left">
            <thead class="text-xs uppercase bg-gray-100">
              <tr>
                <th>Producto</th>
                <th>Cod Barras</th>
                <th>Stock Total</th>
                <th>Disp./Reserv. (Total)</th>
                <th>Acciones</th>
                <th>Referencia</th>
                <th>Nombre</th>
                <th>Variante</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Entrada de Stock --}}
  <div class="modal fade" id="modalEntrada" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formEntrada">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Entrada de Stock</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="entrada_stock_id" name="stock_id">
            
            <div class="mb-3">
              <label class="form-label">Producto</label>
              <input type="text" class="form-control" id="entrada_producto" readonly>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Cantidad <span class="text-danger">*</span></label>
              <input type="number" class="form-control" name="cantidad" min="1" required>
              <small class="text-muted">Stock actual: <span id="entrada_stock_actual"></span></small>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Referencia (Factura/Orden)</label>
              <input type="text" class="form-control" name="referencia">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Motivo/Observaciones</label>
              <textarea class="form-control" name="motivo" rows="2"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Registrar Entrada</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal Salida de Stock --}}
  <div class="modal fade" id="modalSalida" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formSalida">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Salida de Stock</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="salida_stock_id" name="stock_id">
            
            <div class="mb-3">
              <label class="form-label">Producto</label>
              <input type="text" class="form-control" id="salida_producto" readonly>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Cantidad <span class="text-danger">*</span></label>
              <input type="number" class="form-control" name="cantidad" min="1" required>
              <small class="text-muted">Stock disponible: <span id="salida_stock_disponible"></span></small>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Referencia (Factura/Orden)</label>
              <input type="text" class="form-control" name="referencia">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Motivo/Observaciones</label>
              <textarea class="form-control" name="motivo" rows="2"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Registrar Salida</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal Ajuste de Stock --}}
  <div class="modal fade" id="modalAjuste" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formAjuste">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Ajuste de Inventario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="ajuste_stock_id" name="stock_id">
            
            <div class="mb-3">
              <label class="form-label">Producto</label>
              <input type="text" class="form-control" id="ajuste_producto" readonly>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Stock Actual</label>
              <input type="text" class="form-control" id="ajuste_stock_actual" readonly>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Nueva Cantidad <span class="text-danger">*</span></label>
              <input type="number" class="form-control" name="nueva_cantidad" min="0" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Motivo del Ajuste <span class="text-danger">*</span></label>
              <textarea class="form-control" name="motivo" rows="3" required 
                        placeholder="Explique el motivo del ajuste de inventario"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Realizar Ajuste</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal Configuración de Stock --}}
  <div class="modal fade" id="modalConfiguracion" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formConfiguracion">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Configuración de Stock</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="config_stock_id" name="stock_id">
            
            <div class="mb-3">
              <label class="form-label">Producto</label>
              <input type="text" class="form-control" id="config_producto" readonly>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="stock_minimo" id="config_stock_minimo" min="0" required>
              </div>
              
              <div class="col-md-6 mb-3">
                <label class="form-label">Stock Máximo</label>
                <input type="number" class="form-control" name="stock_maximo" id="config_stock_maximo" min="0">
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Ubicación de este registro</label>
              <input type="text" class="form-control" id="config_ubicacion_nombre" readonly>
              <small class="text-muted">La ubicación principal no se cambia desde aquí. Cada registro opera en su ubicación.</small>
            </div>

            <div class="mb-3">
              <label class="form-label">Ubicación Específica</label>
              <input type="text" class="form-control" name="ubicacion" id="config_ubicacion"
                     placeholder="Ej: Pasillo A, Estante 3">
              <small class="text-muted">Detalle dentro de la ubicación (pasillo, estante, etc.)</small>
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="alerta_stock_bajo" 
                       id="config_alerta" value="1" checked>
                <label class="form-check-label" for="config_alerta">
                  Activar alerta de stock bajo
                </label>
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Notas</label>
              <textarea class="form-control" name="notas" id="config_notas" rows="2"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-info">Guardar Configuración</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal Ubicaciones (desglose de stock por ubicación + operaciones) --}}
  <div class="modal fade" id="modalUbicaciones" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-geo-alt"></i> Stock por Ubicación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contenidoUbicaciones">
          <div class="text-center py-4">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Historial --}}
  <div class="modal fade" id="modalHistorial" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Historial de Movimientos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contenidoHistorial">
          <div class="text-center">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Reservas --}}
  <div class="modal fade" id="modalReservas" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-bookmark-check"></i> Reservas de Stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="contenidoReservas">
          <div class="text-center">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal escanear / capturar código de barras -->
  <div class="modal fade" id="modalCodigoBarras" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-upc-scan"></i> Escanear Código de Barras</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="codigoActualWrapper" class="alert alert-info d-none mb-3">
            <small class="d-block text-muted">Código actual:</small>
            <strong id="codigoActualValor" style="font-size: 1.1rem;"></strong>
            <div class="small text-muted mt-1">Escanea o escribe el nuevo código para reemplazarlo.</div>
          </div>

          <label class="form-label fw-bold">Lectura del código:</label>
          <input type="text" id="inputCodigoBarras"
                 class="form-control form-control-lg"
                 autocomplete="off"
                 inputmode="numeric"
                 placeholder="Escanea o escribe el código...">
          <div class="form-text">El guardado es automático al detectar el código (Enter o lectura del scanner).</div>

          <div id="spinnerGuardandoCodigo" class="text-center mt-3 d-none">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
              <span class="visually-hidden">Guardando...</span>
            </div>
            <span class="ms-2 text-muted">Guardando...</span>
          </div>

          <div id="mensajeCodigoBarras" class="alert alert-warning mt-3 d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnEliminarCodigoBarras" class="btn btn-outline-danger me-auto d-none" onclick="eliminarCodigoBarras()">
            <i class="bi bi-trash"></i> Eliminar código
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal historial de código de barras -->
  <div class="modal fade" id="modalCodigoBarrasHistorial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-journal-text"></i> Historial de Código de Barras</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="modalCodigoBarrasHistorialContent">
          <div class="text-center py-4">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  @push('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <style>
    /* Soporte para modales apilados (modal de ubicaciones + entrada/salida/ajuste/config encima) */
    .modal-stacked { z-index: 1080 !important; }
    .modal-backdrop-stacked { z-index: 1075 !important; }
  </style>
  @endpush

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    // Obtener parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const productoId = urlParams.get('producto_id');
    const estadoFiltro = urlParams.get('estado');

    // Estado del modal de ubicaciones (para refrescar tras operaciones)
    let ubicacionesContextoActual = null;
    
    // Configurar Select2 para búsqueda de productos
    $('.select2-productos').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: '-- Todos los productos --',
      allowClear: true,
      ajax: {
        url: '{{ route("stock.productos-json") }}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term
          };
        },
        processResults: function (data) {
          return {
            results: data.results
          };
        },
        cache: true
      },
      minimumInputLength: 0,
      language: {
        searching: function() {
          return "Buscando...";
        },
        noResults: function() {
          return "No se encontraron resultados";
        },
        inputTooShort: function() {
          return "Escribe para buscar";
        }
      }
    });

    // Si hay un filtro de estado, seleccionarlo
    if (estadoFiltro) {
      $('#filtroEstado').val(estadoFiltro);
    }

    // Configurar DataTable
    const table = $('#stock-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      scrollX: true,
      ajax: {
        url: "{{ route('stock.index') }}",
        data: function(d) {
          if (productoId) {
            d.producto_id = productoId;
          }
          const estado = $('#filtroEstado').val();
          if (estado) {
            d.estado = estado;
          }
        }
      },
      columns: [
        { data: 'producto_info', name: 'producto_id' },
        { data: 'codigo_barras', name: 'codigo_barras', orderable: false },
        { data: 'stock_actual', orderable: false, searchable: false },
        { data: 'disponible_reservado', orderable: false, searchable: false },
        { data: 'action', orderable: false, searchable: false },
        { data: 'referencia', visible: false, searchable: false },
        { data: 'nombre_producto', visible: false, searchable: false },
        { data: 'variante_nombre', visible: false, searchable: false }
      ],
      dom: "<'flex justify-between mb-4'<'relative'B>f>t<'flex justify-between items-center px-2 my-2'i<'pagination-wrapper'p>>",
      buttons: [
        { extend: 'pageLength', className: 'btn btn-outline-dark', text: 'Filas' },
        { extend: 'colvis', className: 'btn btn-outline-dark', text: 'Columnas' },
        {
          extend: 'excelHtml5',
          className: 'btn btn-outline-success',
          text: 'Excel',
          title: 'Inventario',
          exportOptions: {
            columns: [5, 6, 7, 1, 2, 3]
          }
        },
        {
          text: 'Inicializar Stock', className: 'btn btn-outline-warning',
          action: () => {
            if (confirm('¿Desea inicializar el stock para todos los productos?')) {
              inicializarStock();
            }
          }
        }
      ],
      language: { url: '{{ asset("js/datatables/es-ES.json") }}' },
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
      drawCallback: function(settings) {
        // Actualizar contadores
        const info = this.api().page.info();
        $('#totalItems').text(info.recordsTotal);
        
        // Calcular productos con stock
        const data = this.api().rows({page:'current'}).data();
        let conStock = 0;
        data.each(function(row) {
          // Extraer el número del badge de stock
          const match = row.stock_actual.match(/>(\d+)</);
          if (match && parseInt(match[1]) > 0) {
            conStock++;
          }
        });
        $('#productosConStock').text(conStock);
      }
    });

    // Función para aplicar filtros
    window.aplicarFiltros = function() {
      const productoId = $('#filtroProducto').val();
      const estado = $('#filtroEstado').val();

      let url = "{{ route('stock.index') }}";
      const params = [];

      if (productoId) {
        params.push('producto_id=' + productoId);
      }
      if (estado) {
        params.push('estado=' + estado);
      }

      if (params.length > 0) {
        url += '?' + params.join('&');
      }

      window.location.href = url;
    };

    // Función para limpiar filtros
    window.limpiarFiltros = function() {
      window.location.href = "{{ route('stock.index') }}";
    };

    // Funciones para los modales
    window.entradaStock = function(stockId) {
      $.get(`/stock/${stockId}/obtener`, function(data) {
        $('#entrada_stock_id').val(stockId);
        $('#entrada_producto').val(data.producto_nombre + (data.variante_nombre ? ' - ' + data.variante_nombre : ''));
        $('#entrada_stock_actual').text(data.stock.cantidad_disponible);
        $('#modalEntrada').modal('show');
      });
    };

    window.salidaStock = function(stockId) {
      $.get(`/stock/${stockId}/obtener`, function(data) {
        $('#salida_stock_id').val(stockId);
        $('#salida_producto').val(data.producto_nombre + (data.variante_nombre ? ' - ' + data.variante_nombre : ''));
        $('#salida_stock_disponible').text(data.stock.stock_real);
        $('#modalSalida').modal('show');
      });
    };

    window.ajusteStock = function(stockId) {
      $.get(`/stock/${stockId}/obtener`, function(data) {
        $('#ajuste_stock_id').val(stockId);
        $('#ajuste_producto').val(data.producto_nombre + (data.variante_nombre ? ' - ' + data.variante_nombre : ''));
        $('#ajuste_stock_actual').val(data.stock.cantidad_disponible);
        $('#modalAjuste').modal('show');
      });
    };

    window.configurarStock = function(stockId) {
      $.get(`/stock/${stockId}/obtener`, function(data) {
        $('#config_stock_id').val(stockId);
        $('#config_producto').val(data.producto_nombre + (data.variante_nombre ? ' - ' + data.variante_nombre : ''));
        $('#config_stock_minimo').val(data.stock.stock_minimo);
        $('#config_stock_maximo').val(data.stock.stock_maximo);
        $('#config_ubicacion_nombre').val(data.ubicacion_nombre || 'Sin ubicación');
        $('#config_ubicacion').val(data.stock.ubicacion);
        $('#config_alerta').prop('checked', data.stock.alerta_stock_bajo);
        $('#config_notas').val(data.stock.notas);
        $('#modalConfiguracion').modal('show');
      });
    };

    window.verHistorial = function(productoId, varianteId) {
      $.get('/stock/historial', { producto_id: productoId, variante_id: varianteId }, function(response) {
        $('#contenidoHistorial').html(response.html);
        $('#modalHistorial').modal('show');
      });
    };

    // Contexto del modal de reservas (para recargar tras liberar)
    let reservasContextoActual = null;

    window.verReservas = function(productoId, varianteId) {
      reservasContextoActual = {
        producto_id: productoId,
        variante_producto_id: (varianteId && varianteId !== 'null') ? varianteId : null
      };
      $('#contenidoReservas').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
      $('#modalReservas').modal('show');
      cargarReservas();
    };

    function cargarReservas() {
      if (!reservasContextoActual) return;
      const params = { producto_id: reservasContextoActual.producto_id };
      if (reservasContextoActual.variante_producto_id) params.variante_producto_id = reservasContextoActual.variante_producto_id;
      $.get('/stock/reservas', params, function(response) {
        $('#contenidoReservas').html(response.html);
      }).fail(function() {
        $('#contenidoReservas').html('<p class="text-center text-danger">Error al cargar las reservas.</p>');
      });
    }

    $('#modalReservas').on('hidden.bs.modal', function() {
      reservasContextoActual = null;
    });

    // Abre el modal con el desglose de stock por ubicación y los botones de operación
    window.verUbicaciones = function(productoId, varianteId) {
      ubicacionesContextoActual = {
        producto_id: productoId,
        variante_producto_id: (varianteId && varianteId !== 'null') ? varianteId : null
      };
      $('#contenidoUbicaciones').html('<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>');
      $('#modalUbicaciones').modal('show');
      cargarUbicaciones();
    };

    function cargarUbicaciones() {
      if (!ubicacionesContextoActual) return;
      const params = { producto_id: ubicacionesContextoActual.producto_id };
      if (ubicacionesContextoActual.variante_producto_id) {
        params.variante_producto_id = ubicacionesContextoActual.variante_producto_id;
      }
      $.get("{{ route('stock.ubicaciones-ajax') }}", params, function(response) {
        $('#contenidoUbicaciones').html(response.html);
      }).fail(function() {
        $('#contenidoUbicaciones').html('<p class="text-center text-danger">Error al cargar las ubicaciones.</p>');
      });
    }

    // Refresca el modal de ubicaciones y la tabla principal después de una operación
    window.refrescarTrasOperacion = function() {
      if (ubicacionesContextoActual) {
        cargarUbicaciones();
      }
      table.ajax.reload(null, false);
    };

    // Limpiar contexto al cerrar modal de ubicaciones
    $('#modalUbicaciones').on('hidden.bs.modal', function() {
      ubicacionesContextoActual = null;
    });

    // Soporte para modales apilados: cuando se abre un modal encima del de ubicaciones,
    // elevar su z-index y el de su backdrop para que queden visibles arriba.
    ['#modalEntrada', '#modalSalida', '#modalAjuste', '#modalConfiguracion'].forEach(function(sel) {
      $(sel).on('shown.bs.modal', function() {
        if ($('#modalUbicaciones').hasClass('show')) {
          $(this).css('z-index', 1080);
          // Mover el backdrop más reciente al z-index correcto
          const backdrops = $('.modal-backdrop');
          if (backdrops.length > 1) {
            backdrops.last().css('z-index', 1075);
          }
          // Reasegurar foco
          $('body').addClass('modal-open');
        }
      });
      $(sel).on('hidden.bs.modal', function() {
        $(this).css('z-index', '');
        // Si el modal de ubicaciones sigue abierto, mantener body con modal-open
        if ($('#modalUbicaciones').hasClass('show')) {
          $('body').addClass('modal-open');
        }
      });
    });

    // Agregar una ubicación nueva al producto/variante actual
    window.agregarUbicacionAlProducto = function(productoId, varianteId) {
      const ubicacionId = $('#ubicacion-nueva-select').val();
      if (!ubicacionId) {
        if (typeof toastr !== 'undefined') toastr.warning('Selecciona una ubicación'); else alert('Selecciona una ubicación');
        return;
      }
      $.ajax({
        url: "{{ route('stock.agregar-ubicacion') }}",
        method: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          producto_id: productoId,
          variante_producto_id: (varianteId && varianteId !== 'null') ? varianteId : null,
          ubicacion_id: ubicacionId
        },
        success: function(response) {
          if (response.success) {
            if (typeof toastr !== 'undefined') toastr.success(response.message); else alert(response.message);
            refrescarTrasOperacion();
          }
        },
        error: function(xhr) {
          const message = (xhr.responseJSON ? xhr.responseJSON.message : 'Error desconocido');
          if (typeof toastr !== 'undefined') toastr.error(message); else alert(message);
        }
      });
    };

    window.liberarReservaDesdeStock = function(solicitudId, stockId) {
      Swal.fire({
        title: '¿Quitar reserva de stock?',
        text: 'Se liberará la reserva de stock de esta cotización. El stock reservado volverá a estar disponible.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, quitar reserva',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Liberando reserva...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
          });

          $.post('/solicitudes/' + solicitudId + '/liberar-reserva', {
            _token: $('meta[name="csrf-token"]').attr('content')
          }, function(response) {
            if (response.success) {
              Swal.fire('Reserva liberada', response.mensaje, 'success');
              cargarReservas();
              if (typeof table !== 'undefined') {
                table.ajax.reload(null, false);
              }
            }
          }).fail(function(xhr) {
            Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.mensaje : 'Error al liberar la reserva', 'error');
          });
        }
      });
    };

    function enviarFormulario(formId, url, modalId) {
      $.ajax({
        url: url,
        method: 'POST',
        data: $(formId).serialize(),
        success: function(response) {
          if (response.success) {
            $(modalId).modal('hide');
            refrescarTrasOperacion();
            if (typeof toastr !== 'undefined') {
              toastr.success(response.message);
            } else {
              alert(response.message);
            }
          }
        },
        error: function(xhr) {
          const message = 'Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Error desconocido');
          if (typeof toastr !== 'undefined') {
            toastr.error(message);
          } else {
            alert(message);
          }
        }
      });
    }

    $('#formEntrada').on('submit', function(e) {
      e.preventDefault();
      enviarFormulario('#formEntrada', "{{ route('stock.entrada') }}", '#modalEntrada');
    });

    $('#formSalida').on('submit', function(e) {
      e.preventDefault();
      enviarFormulario('#formSalida', "{{ route('stock.salida') }}", '#modalSalida');
    });

    $('#formAjuste').on('submit', function(e) {
      e.preventDefault();
      enviarFormulario('#formAjuste', "{{ route('stock.ajuste') }}", '#modalAjuste');
    });

    $('#formConfiguracion').on('submit', function(e) {
      e.preventDefault();
      enviarFormulario('#formConfiguracion', "{{ route('stock.configurar') }}", '#modalConfiguracion');
    });

    // Inicializar stock
    function inicializarStock() {
      $.ajax({
        url: "{{ route('stock.inicializar-todos') }}",
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
          if (response.success) {
            table.ajax.reload();
            if (typeof toastr !== 'undefined') {
              toastr.success(response.message);
            } else {
              alert(response.message);
            }
          }
        },
        error: function(xhr) {
          const message = 'Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Error desconocido');
          if (typeof toastr !== 'undefined') {
            toastr.error(message);
          } else {
            alert(message);
          }
        }
      });
    }

    // Atajos de teclado
    $(document).keydown(function(e) {
      // Alt + F para abrir filtros
      if (e.altKey && e.key === 'f') {
        e.preventDefault();
        $('#filtroProducto').select2('open');
      }
      // Alt + L para limpiar filtros
      if (e.altKey && e.key === 'l') {
        e.preventDefault();
        limpiarFiltros();
      }
    });
  });

  // ====== Código de Barras: escaneo rápido + historial (módulo Stock) ======
  let stockIdEscaneado = null;
  let lastInputTimeCb = 0;
  let debounceTimerCb = null;
  let enviandoCodigoCb = false;

  function abrirModalCodigoBarras(stockId, codigoActual) {
    stockIdEscaneado = stockId;
    enviandoCodigoCb = false;

    const $wrapper = $('#codigoActualWrapper');
    const $btnEliminar = $('#btnEliminarCodigoBarras');
    if (codigoActual && codigoActual.length > 0) {
      $('#codigoActualValor').text(codigoActual);
      $wrapper.removeClass('d-none');
      $btnEliminar.removeClass('d-none').prop('disabled', false);
    } else {
      $wrapper.addClass('d-none');
      $('#codigoActualValor').text('');
      $btnEliminar.addClass('d-none').prop('disabled', false);
    }

    $('#inputCodigoBarras').val('').prop('disabled', false);
    $('#spinnerGuardandoCodigo').addClass('d-none');
    $('#mensajeCodigoBarras').addClass('d-none').text('');

    $('#modalCodigoBarras').modal('show');
  }

  function eliminarCodigoBarras() {
    if (!stockIdEscaneado) return;
    if (enviandoCodigoCb) return;

    const ejecutar = () => {
      enviandoCodigoCb = true;
      $('#btnEliminarCodigoBarras').prop('disabled', true);
      $('#inputCodigoBarras').prop('disabled', true);
      $('#spinnerGuardandoCodigo').removeClass('d-none');
      $('#mensajeCodigoBarras').addClass('d-none').text('');

      fetch(`/stock/${stockIdEscaneado}/codigo-barras`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        return { ok: res.ok, status: res.status, data };
      })
      .then(({ ok, status, data }) => {
        $('#spinnerGuardandoCodigo').addClass('d-none');
        $('#inputCodigoBarras').prop('disabled', false);
        $('#btnEliminarCodigoBarras').prop('disabled', false);
        enviandoCodigoCb = false;

        if (ok && data.success) {
          $('#modalCodigoBarras').modal('hide');
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Código eliminado',
              text: data.message || 'Código de barras eliminado correctamente.',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 2000
            });
          }
          $('#stock-table').DataTable().ajax.reload(null, false);
        } else {
          const msg = data.message || 'No se pudo eliminar el código de barras.';
          $('#mensajeCodigoBarras').removeClass('d-none').text(msg);
        }
      })
      .catch(() => {
        $('#spinnerGuardandoCodigo').addClass('d-none');
        $('#inputCodigoBarras').prop('disabled', false);
        $('#btnEliminarCodigoBarras').prop('disabled', false);
        enviandoCodigoCb = false;
        $('#mensajeCodigoBarras').removeClass('d-none').text('Error de conexión. Intenta nuevamente.');
      });
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: '¿Eliminar el código de barras?',
        text: 'Se quitará el código actual y quedará registrado en el historial.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
      }).then((result) => {
        if (result.isConfirmed) ejecutar();
      });
    } else if (confirm('¿Eliminar el código de barras actual?')) {
      ejecutar();
    }
  }

  function enviarCodigoBarrasStock() {
    if (enviandoCodigoCb) return;
    const valor = ($('#inputCodigoBarras').val() || '').trim();
    if (!valor) return;
    if (!stockIdEscaneado) return;

    enviandoCodigoCb = true;
    $('#inputCodigoBarras').prop('disabled', true);
    $('#spinnerGuardandoCodigo').removeClass('d-none');
    $('#mensajeCodigoBarras').addClass('d-none').text('');

    fetch(`/stock/${stockIdEscaneado}/codigo-barras`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ codigo_barras: valor })
    })
    .then(async (res) => {
      const data = await res.json().catch(() => ({}));
      return { ok: res.ok, status: res.status, data };
    })
    .then(({ ok, status, data }) => {
      $('#spinnerGuardandoCodigo').addClass('d-none');
      $('#inputCodigoBarras').prop('disabled', false);
      enviandoCodigoCb = false;

      if (ok && data.success) {
        $('#modalCodigoBarras').modal('hide');
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Código guardado',
            text: data.message || 'Código de barras guardado correctamente.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
          });
        }
        $('#stock-table').DataTable().ajax.reload(null, false);
      } else if (status === 422) {
        const msg = data.message || 'No se pudo guardar el código de barras.';
        $('#mensajeCodigoBarras').removeClass('d-none').text(msg);
        $('#inputCodigoBarras').val('').focus();
      } else {
        $('#mensajeCodigoBarras').removeClass('d-none').text('Ocurrió un error al guardar. Intenta nuevamente.');
        $('#inputCodigoBarras').focus();
      }
    })
    .catch(() => {
      $('#spinnerGuardandoCodigo').addClass('d-none');
      $('#inputCodigoBarras').prop('disabled', false);
      enviandoCodigoCb = false;
      $('#mensajeCodigoBarras').removeClass('d-none').text('Error de conexión. Intenta nuevamente.');
      $('#inputCodigoBarras').focus();
    });
  }

  function verHistorialCodigoBarras(stockId) {
    $('#modalCodigoBarrasHistorialContent').html(
      '<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>'
    );
    $('#modalCodigoBarrasHistorial').modal('show');
    $.get(`/stock/${stockId}/codigo-barras-historial-ajax`, function(data) {
      $('#modalCodigoBarrasHistorialContent').html(data);
    }).fail(function() {
      $('#modalCodigoBarrasHistorialContent').html(
        '<div class="alert alert-danger">No se pudo cargar el historial.</div>'
      );
    });
  }

  $(document).ready(function() {
    const $modalCb = $('#modalCodigoBarras');

    $modalCb.on('shown.bs.modal', function() {
      $('#inputCodigoBarras').trigger('focus');
    });

    $modalCb.on('hidden.bs.modal', function() {
      $('#inputCodigoBarras').val('').prop('disabled', false);
      $('#spinnerGuardandoCodigo').addClass('d-none');
      $('#mensajeCodigoBarras').addClass('d-none').text('');
      $('#btnEliminarCodigoBarras').addClass('d-none').prop('disabled', false);
      stockIdEscaneado = null;
      enviandoCodigoCb = false;
      if (debounceTimerCb) {
        clearTimeout(debounceTimerCb);
        debounceTimerCb = null;
      }
    });

    $('#inputCodigoBarras').on('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        if (debounceTimerCb) {
          clearTimeout(debounceTimerCb);
          debounceTimerCb = null;
        }
        enviarCodigoBarrasStock();
      }
    });

    $('#inputCodigoBarras').on('input', function() {
      lastInputTimeCb = Date.now();
      if (debounceTimerCb) clearTimeout(debounceTimerCb);
      debounceTimerCb = setTimeout(function() {
        const valor = ($('#inputCodigoBarras').val() || '').trim();
        if (valor.length >= 6 && (Date.now() - lastInputTimeCb) >= 180) {
          enviarCodigoBarrasStock();
        }
      }, 220);
    });
  });
  </script>
  @endpush
</x-app-layout>
