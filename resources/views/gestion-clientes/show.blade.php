<x-app-layout>
    <x-slot name="header">Detalle de Cliente</x-slot>

    <style>
        .info-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            height: 100%;
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }
        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
        }
        .direccion-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: border-color 0.2s;
        }
        .direccion-card:hover {
            border-color: #3b82f6;
        }
        .direccion-card.predeterminada {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
        }
        .status-pagada { background: #d1fae5; color: #065f46; }
        .status-enviada { background: #dbeafe; color: #1e40af; }
        .status-entregada { background: #d1fae5; color: #065f46; }
        .status-pendiente { background: #fef3c7; color: #92400e; }
        .status-cancelada { background: #fee2e2; color: #991b1b; }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white me-4" style="width: 64px; height: 64px; font-size: 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                {{ strtoupper(substr($cliente->nombre_cliente, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="mb-1">{{ $cliente->nombre_cliente }}</h3>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-envelope me-1"></i> {{ $cliente->email_cliente }}
                                    <span class="mx-2">|</span>
                                    <i class="bi bi-telephone me-1"></i> {{ $cliente->telefono_cliente }}
                                </p>
                                <small class="text-muted">
                                    Cliente desde {{ \Carbon\Carbon::parse($cliente->primera_compra)->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('gestion-clientes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            {{-- Estadisticas del cliente --}}
            <div class="row g-4 mb-6">
                <div class="col-md-3">
                    <div class="info-card text-center">
                        <div class="stat-value text-primary">{{ $cliente->total_compras }}</div>
                        <div class="stat-label">Compras Totales</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card text-center">
                        <div class="stat-value text-success">${{ number_format($cliente->total_gastado, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Gastado</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card text-center">
                        <div class="stat-value text-info">${{ number_format($cliente->ticket_promedio, 0, ',', '.') }}</div>
                        <div class="stat-label">Ticket Promedio</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card text-center">
                        <div class="stat-value text-warning">{{ \Carbon\Carbon::parse($cliente->ultima_compra)->diffForHumans() }}</div>
                        <div class="stat-label">Ultima Compra</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Historial de Compras --}}
                <div class="col-lg-8">
                    <div class="info-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0"><i class="bi bi-bag-check me-2"></i>Historial de Compras</h5>
                        </div>

                        @if($compras->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-bag-x display-4"></i>
                                <p class="mt-2">No hay compras registradas</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Pedido</th>
                                            <th>Fecha</th>
                                            <th>Productos</th>
                                            <th class="text-end">Total</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($compras as $compra)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('compras.show', $compra) }}" class="text-decoration-none fw-medium">
                                                        #{{ $compra->numero_compra }}
                                                    </a>
                                                </td>
                                                <td>{{ $compra->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <small>{{ $compra->items->count() }} productos</small>
                                                </td>
                                                <td class="text-end fw-bold">${{ number_format($compra->total, 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="status-badge status-{{ $compra->estado }}">
                                                        {{ ucfirst($compra->estado) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($compras->hasPages())
                                <div class="mt-3">
                                    {{ $compras->links() }}
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Productos Favoritos --}}
                    @if($productosFavoritos->isNotEmpty())
                        <div class="info-card mt-4">
                            <h5 class="mb-4"><i class="bi bi-heart me-2 text-danger"></i>Productos Favoritos</h5>
                            <div class="row g-3">
                                @foreach($productosFavoritos as $producto)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-2 border rounded">
                                            <div class="flex-grow-1">
                                                <div class="fw-medium">{{ $producto->nombre }}</div>
                                                <small class="text-muted">Ref: {{ $producto->referencia }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary">{{ $producto->cantidad_total }} unid.</span>
                                                <br>
                                                <small class="text-muted">{{ $producto->veces_comprado }} compras</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Direcciones --}}
                <div class="col-lg-4">
                    <div class="info-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Direcciones</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaDireccion">
                                <i class="bi bi-plus"></i> Nueva
                            </button>
                        </div>

                        @if($direcciones->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-geo-alt display-4"></i>
                                <p class="mt-2">No hay direcciones guardadas</p>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaDireccion">
                                    <i class="bi bi-plus"></i> Agregar direccion
                                </button>
                            </div>
                        @else
                            @foreach($direcciones as $direccion)
                                <div class="direccion-card {{ $direccion->es_predeterminada ? 'predeterminada' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            @if($direccion->alias)
                                                <span class="badge bg-secondary">{{ $direccion->alias }}</span>
                                            @endif
                                            @if($direccion->es_predeterminada)
                                                <span class="badge bg-success">Predeterminada</span>
                                            @endif
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item" onclick="editarDireccion({{ json_encode($direccion) }})">
                                                        <i class="bi bi-pencil me-2"></i>Editar
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('gestion-clientes.direcciones.destroy', $direccion) }}" method="POST"
                                                          onsubmit="return confirm('¿Eliminar esta direccion?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i>Eliminar
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="fw-medium">{{ $direccion->nombre_destinatario }}</div>
                                    <div class="text-muted small">{{ $direccion->direccion }}</div>
                                    <div class="text-muted small">
                                        {{ $direccion->ciudad->nombre }}, {{ $direccion->ciudad->departamento->nombre }}
                                    </div>
                                    <div class="mt-2">
                                        <a href="tel:{{ $direccion->telefono }}" class="text-decoration-none small">
                                            <i class="bi bi-telephone"></i> {{ $direccion->telefono }}
                                        </a>
                                    </div>
                                    @if($direccion->instrucciones)
                                        <div class="mt-2 small text-muted fst-italic">
                                            {{ $direccion->instrucciones }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Nueva Direccion --}}
    <div class="modal fade" id="modalNuevaDireccion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('gestion-clientes.direcciones.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email_cliente" value="{{ $cliente->email_cliente }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Direccion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Alias</label>
                                <input type="text" class="form-control" name="alias" placeholder="Ej: Casa, Oficina">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre Destinatario *</label>
                                <input type="text" class="form-control" name="nombre_destinatario" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefono *</label>
                                <input type="tel" class="form-control" name="telefono" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ciudad *</label>
                                <select class="form-select" name="ciudad_id" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($ciudades as $departamento => $ciudadesDep)
                                        <optgroup label="{{ $departamento }}">
                                            @foreach($ciudadesDep as $ciudad)
                                                <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Direccion *</label>
                                <textarea class="form-control" name="direccion" rows="2" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Instrucciones de entrega</label>
                                <textarea class="form-control" name="instrucciones" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="es_predeterminada" value="1" id="esPredeterminada">
                                    <label class="form-check-label" for="esPredeterminada">
                                        Establecer como direccion predeterminada
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Guardar Direccion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Editar Direccion --}}
    <div class="modal fade" id="modalEditarDireccion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditarDireccion" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Direccion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Alias</label>
                                <input type="text" class="form-control" name="alias" id="editAlias">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre Destinatario *</label>
                                <input type="text" class="form-control" name="nombre_destinatario" id="editNombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefono *</label>
                                <input type="tel" class="form-control" name="telefono" id="editTelefono" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ciudad *</label>
                                <select class="form-select" name="ciudad_id" id="editCiudad" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($ciudades as $departamento => $ciudadesDep)
                                        <optgroup label="{{ $departamento }}">
                                            @foreach($ciudadesDep as $ciudad)
                                                <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Direccion *</label>
                                <textarea class="form-control" name="direccion" id="editDireccion" rows="2" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Instrucciones de entrega</label>
                                <textarea class="form-control" name="instrucciones" id="editInstrucciones" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="es_predeterminada" value="1" id="editPredeterminada">
                                    <label class="form-check-label" for="editPredeterminada">
                                        Establecer como direccion predeterminada
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function editarDireccion(direccion) {
            const form = document.getElementById('formEditarDireccion');
            form.action = `/gestion-clientes/direcciones/${direccion.id}`;

            document.getElementById('editAlias').value = direccion.alias || '';
            document.getElementById('editNombre').value = direccion.nombre_destinatario;
            document.getElementById('editTelefono').value = direccion.telefono;
            document.getElementById('editCiudad').value = direccion.ciudad_id;
            document.getElementById('editDireccion').value = direccion.direccion;
            document.getElementById('editInstrucciones').value = direccion.instrucciones || '';
            document.getElementById('editPredeterminada').checked = direccion.es_predeterminada;

            const modal = new bootstrap.Modal(document.getElementById('modalEditarDireccion'));
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>
