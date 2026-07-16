<x-app-layout>
  <x-slot name="header">Ferias</x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-2xl font-semibold mb-0">Gestión de Ferias</h4>
            <a href="{{ route('ferias.crear') }}" class="btn btn-primary btn-sm">
              <i class="bi bi-plus-lg"></i> Nueva Feria
            </a>
          </div>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>Feria</th>
                  <th>Ubicación</th>
                  <th>Lista de precios</th>
                  <th>Fechas</th>
                  <th>Estado</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @forelse($ferias as $f)
                  <tr>
                    <td><strong>{{ $f->nombre }}</strong><div class="small text-muted">Creada {{ $f->created_at?->format('d/m/Y') }}</div></td>
                    <td>{{ $f->ubicacion?->nombre ?? '—' }}</td>
                    <td>{{ $f->listaPrecio?->nombre ?? '—' }}</td>
                    <td class="small">
                      {{ $f->fecha_inicio?->format('d/m/Y') ?? '—' }}
                      @if($f->fecha_fin) → {{ $f->fecha_fin->format('d/m/Y') }} @endif
                    </td>
                    <td>{!! $f->estadoBadge() !!}</td>
                    <td class="text-end">
                      <a href="{{ route('ferias.show', $f->id) }}" class="btn btn-sm btn-info text-white" title="Abrir">
                        <i class="bi bi-box-arrow-in-right"></i> Abrir
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-4">Aún no hay ferias. Crea la primera con «Nueva Feria».</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
