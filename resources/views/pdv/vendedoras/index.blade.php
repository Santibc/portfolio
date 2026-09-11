<x-app-layout>
  <x-slot name="header">Vendedoras de Prefactura</x-slot>

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="text-2xl font-semibold mb-0">Vendedoras de Prefactura</h4>
          </div>
          <p class="text-muted small">Estas son las vendedoras que aparecen para escoger al <strong>crear una prefactura</strong>. Cada vendedora <strong>debe tener una sede</strong>: solo aparece en esa tienda. Una vendedora <strong>sin sede asignada NO aparece en ninguna</strong> tienda.</p>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          {{-- Agregar --}}
          <form action="{{ route('pdv.vendedoras.store') }}" method="POST" class="row g-2 align-items-end mb-4">
            @csrf
            <div class="col">
              <label class="form-label small fw-semibold">Nueva vendedora</label>
              <input type="text" name="nombre" class="form-control" maxlength="150" placeholder="Nombre de la vendedora" value="{{ old('nombre') }}" required>
            </div>
            <div class="col-auto">
              <label class="form-label small fw-semibold">Sede</label>
              <select name="ubicacion_id" class="form-select">
                <option value="">— Sin asignar (no aparece) —</option>
                @foreach($ubicaciones as $u)
                  <option value="{{ $u->id }}" @selected(old('ubicacion_id')==$u->id)>{{ $u->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Agregar</button>
            </div>
          </form>

          {{-- Listado --}}
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>Nombre</th>
                  <th>Sede</th>
                  <th style="width:120px;">Estado</th>
                  <th style="width:200px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($vendedoras as $v)
                  <tr>
                    <td>
                      <form action="{{ route('pdv.vendedoras.update', $v->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="text" name="nombre" class="form-control form-control-sm" value="{{ $v->nombre }}" maxlength="150" required style="max-width:220px;">
                        <select name="ubicacion_id" class="form-select form-select-sm" style="max-width:200px;">
                          <option value="">— Sin asignar (no aparece) —</option>
                          @foreach($ubicaciones as $u)
                            <option value="{{ $u->id }}" @selected($v->ubicacion_id==$u->id)>{{ $u->nombre }}</option>
                          @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Guardar cambios"><i class="bi bi-check-lg"></i></button>
                      </form>
                    </td>
                    <td>
                      @if($v->ubicacion)
                        <span class="badge bg-info text-dark">{{ $v->ubicacion->nombre }}</span>
                      @else
                        <span class="badge bg-warning text-dark">Sin asignar · no aparece</span>
                      @endif
                    </td>
                    <td>
                      @if($v->activo)
                        <span class="badge bg-success">Activa</span>
                      @else
                        <span class="badge bg-secondary">Inactiva</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        <form action="{{ route('pdv.vendedoras.toggle', $v->id) }}" method="POST">
                          @csrf
                          @if($v->activo)
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Desactivar"><i class="bi bi-pause-circle"></i> Desactivar</button>
                          @else
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Activar"><i class="bi bi-play-circle"></i> Activar</button>
                          @endif
                        </form>
                        <form action="{{ route('pdv.vendedoras.destroy', $v->id) }}" method="POST" onsubmit="return confirm('¿Eliminar a {{ $v->nombre }}? Las prefacturas ya creadas conservan su nombre.')">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-4">No hay vendedoras. Agrega la primera arriba.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
