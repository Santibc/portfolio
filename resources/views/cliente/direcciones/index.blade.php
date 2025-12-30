@extends('cliente.layout')

@section('title', 'Mis Direcciones')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="bi bi-geo-alt"></i> Mis Direcciones</h2>
            <p class="text-muted mb-0">Administra tus direcciones de entrega</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDireccion">
            <i class="bi bi-plus-lg"></i> Nueva dirección
        </button>
    </div>
@endsection

@section('content')
@if($direcciones->isEmpty())
    <div class="content-card text-center py-5">
        <i class="bi bi-geo-alt text-muted" style="font-size: 4rem;"></i>
        <h5 class="mt-3">No tienes direcciones guardadas</h5>
        <p class="text-muted">Agrega tu primera dirección para hacer tus compras más rápido</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDireccion">
            <i class="bi bi-plus-lg"></i> Agregar dirección
        </button>
    </div>
@else
    <div class="row g-4">
        @foreach($direcciones as $direccion)
        <div class="col-md-6">
            <div class="content-card h-100 {{ $direccion->es_predeterminada ? 'border-primary border-2' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1">
                            <i class="bi bi-{{ $direccion->alias == 'Trabajo' ? 'building' : ($direccion->alias == 'Casa' ? 'house' : 'geo-alt') }}"></i>
                            {{ $direccion->alias ?? 'Dirección' }}
                        </h5>
                        @if($direccion->es_predeterminada)
                            <span class="badge bg-primary">Predeterminada</span>
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button class="dropdown-item" onclick="editarDireccion({{ $direccion->id }})">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            </li>
                            @if(!$direccion->es_predeterminada)
                            <li>
                                <form action="{{ route('cliente.direcciones.predeterminada', $direccion) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-star"></i> Marcar como predeterminada
                                    </button>
                                </form>
                            </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('cliente.direcciones.destroy', $direccion) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta dirección?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <p class="mb-1"><strong>{{ $direccion->nombre_destinatario }}</strong></p>
                <p class="mb-1">{{ $direccion->direccion }}</p>
                <p class="mb-1 text-muted">{{ $direccion->ciudad->nombre ?? '' }}</p>
                <p class="mb-0"><i class="bi bi-telephone"></i> {{ $direccion->telefono }}</p>

                @if($direccion->instrucciones)
                    <hr>
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-info-circle"></i> {{ $direccion->instrucciones }}
                    </p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@endif

{{-- Modal Agregar/Editar Dirección --}}
<div class="modal fade" id="modalDireccion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formDireccion" action="{{ route('cliente.direcciones.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nueva dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alias</label>
                        <select name="alias" class="form-select">
                            <option value="Casa">Casa</option>
                            <option value="Trabajo">Trabajo</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del destinatario *</label>
                        <input type="text" name="nombre_destinatario" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Teléfono *</label>
                        <input type="tel" name="telefono" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección *</label>
                        <textarea name="direccion" class="form-control" rows="2" required
                                  placeholder="Calle, carrera, número, apartamento..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ciudad *</label>
                        <select name="ciudad_id" class="form-select" required>
                            <option value="">Seleccionar ciudad</option>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instrucciones de entrega</label>
                        <textarea name="instrucciones" class="form-control" rows="2"
                                  placeholder="Indicaciones adicionales para el repartidor..."></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="es_predeterminada" value="1" class="form-check-input" id="esPredeterminada">
                        <label class="form-check-label" for="esPredeterminada">
                            Usar como dirección predeterminada
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const direcciones = @json($direcciones);

function editarDireccion(id) {
    const dir = direcciones.find(d => d.id === id);
    if (!dir) return;

    document.getElementById('modalTitle').textContent = 'Editar dirección';
    document.getElementById('formDireccion').action = `/cliente/direcciones/${id}`;
    document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    const form = document.getElementById('formDireccion');
    form.querySelector('[name="alias"]').value = dir.alias || 'Casa';
    form.querySelector('[name="nombre_destinatario"]').value = dir.nombre_destinatario;
    form.querySelector('[name="telefono"]').value = dir.telefono;
    form.querySelector('[name="direccion"]').value = dir.direccion;
    form.querySelector('[name="ciudad_id"]').value = dir.ciudad_id;
    form.querySelector('[name="instrucciones"]').value = dir.instrucciones || '';
    form.querySelector('[name="es_predeterminada"]').checked = dir.es_predeterminada;

    new bootstrap.Modal(document.getElementById('modalDireccion')).show();
}

// Reset form on modal close
document.getElementById('modalDireccion').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitle').textContent = 'Nueva dirección';
    document.getElementById('formDireccion').action = '{{ route('cliente.direcciones.store') }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('formDireccion').reset();
});
</script>
@endpush
@endsection
