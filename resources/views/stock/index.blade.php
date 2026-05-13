<x-app-layout>
  <x-slot name="header">Gestión de Stock</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-4">
          {{ session('warning') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- Tarjetas resumen --}}
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card border-warning">
            <div class="card-body">
              <h6 class="card-title text-warning">
                <i class="bi bi-exclamation-triangle"></i> Stock Bajo
              </h6>
              <p class="card-text display-6">{{ $productosConStockBajo }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-danger">
            <div class="card-body">
              <h6 class="card-title text-danger">
                <i class="bi bi-x-circle"></i> Sin Stock
              </h6>
              <p class="card-text display-6">{{ $productosSinStock }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-info">
            <div class="card-body">
              <h6 class="card-title text-info">
                <i class="bi bi-clock-history"></i> Historial
              </h6>
              <a href="{{ route('stock.historial') }}" class="btn btn-outline-info">
                Ver movimientos
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- Importar Excel --}}
      <div class="card shadow">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-upload"></i> Importar / Actualizar inventario desde Excel</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('stock.importar-excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label class="form-label">Archivo Excel / CSV</label>
              <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-upload"></i> Subir e importar
            </button>
          </form>

          <hr>

          <h6 class="mt-3">Formato esperado</h6>
          <p class="mb-2 text-muted">Encabezados en la fila 1. La columna <code>referencia</code> puede ser el código del producto o el SKU de una variante.</p>
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Columna</th>
                  <th>Obligatorio</th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody>
                <tr><td><code>referencia</code></td><td>Sí</td><td>Referencia del producto o SKU de la variante.</td></tr>
                <tr><td><code>cantidad</code></td><td>Sí</td><td>Cantidad disponible (entero positivo).</td></tr>
                <tr><td><code>modo</code></td><td>No</td><td><code>set</code> (reemplaza, default), <code>sumar</code> o <code>restar</code>.</td></tr>
                <tr><td><code>stock_minimo</code></td><td>No</td><td>Cantidad mínima para alerta de stock bajo.</td></tr>
                <tr><td><code>stock_maximo</code></td><td>No</td><td>Cantidad máxima de referencia.</td></tr>
                <tr><td><code>ubicacion</code></td><td>No</td><td>Ubicación física (bodega, estante, etc).</td></tr>
              </tbody>
            </table>
          </div>

          @if(session('errores_stock'))
            <div class="alert alert-warning mt-3">
              <strong>Errores en el archivo:</strong>
              <ul class="mb-0">
                @foreach(session('errores_stock') as $err)
                  <li>Fila {{ $err['fila'] }} — <code>{{ $err['referencia'] }}</code>: {{ $err['mensaje'] }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
