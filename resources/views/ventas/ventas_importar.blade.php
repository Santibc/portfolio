<x-app-layout>
    <x-slot name="header">Carga masiva de Ventas</x-slot>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="p-6">
            <h4 class="text-2xl font-semibold mb-4">Carga masiva de Ventas</h4>

            @if (session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @isset($procesadas)
              <div class="alert {{ empty($errores) ? 'alert-success' : 'alert-warning' }}">
                <strong>{{ $procesadas }}</strong> venta(s) procesada(s) correctamente.
                @if (!empty($errores))
                  <strong>{{ count($errores) }}</strong> fila(s) rechazada(s).
                @endif
              </div>
              @if (!empty($errores))
                <div class="alert alert-danger">
                  <strong>Errores detectados:</strong>
                  <ul class="mb-0 mt-2">
                    @foreach ($errores as $err)
                      <li>{{ $err }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
            @endisset

            <p class="text-muted">
              Descargue la plantilla y complete las columnas: <code>vendedor_email</code>, <code>fecha</code> (YYYY-MM-DD),
              <code>monto</code>, <code>descripcion</code> (opcional), <code>almacen_codigo</code> (opcional; si se omite se hereda del vendedor).
            </p>

            <div class="mb-3">
              <a href="{{ route('ventas.importar.plantilla') }}" class="btn btn-outline-success">
                <i class="bi bi-download"></i> Descargar plantilla Excel
              </a>
            </div>

            <form method="POST" action="{{ route('ventas.importar.procesar') }}" enctype="multipart/form-data">
              @csrf
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Archivo (Excel o CSV) <span class="text-danger">*</span></label>
                  <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv,.txt" required>
                  @error('archivo')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-upload"></i> Cargar
                </button>
                <a href="{{ route('ventas') }}" class="btn btn-outline-secondary">Volver</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</x-app-layout>
