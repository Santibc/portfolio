<x-app-layout>
    <x-slot name="header">Gestion de Clientes</x-slot>

    <style>
        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .cliente-row:hover {
            background-color: #f9fafb;
            cursor: pointer;
        }
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Estadisticas --}}
            <div class="row g-3 mb-6">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stats-card h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Clientes</p>
                                <p class="h4 fw-bold text-gray-900 mb-0">{{ number_format($estadisticas['total_clientes']) }}</p>
                            </div>
                            <div class="text-primary">
                                <i class="bi bi-people-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stats-card h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Clientes Recurrentes</p>
                                <p class="h4 fw-bold text-success mb-0">{{ number_format($estadisticas['clientes_recurrentes']) }}</p>
                            </div>
                            <div class="text-success">
                                <i class="bi bi-arrow-repeat fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stats-card h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Ticket Promedio</p>
                                <p class="h4 fw-bold text-info mb-0">${{ number_format($estadisticas['ticket_promedio'], 0, ',', '.') }}</p>
                            </div>
                            <div class="text-info">
                                <i class="bi bi-receipt fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="stats-card h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Nuevos este Mes</p>
                                <p class="h4 fw-bold text-warning mb-0">{{ number_format($estadisticas['nuevos_mes']) }}</p>
                            </div>
                            <div class="text-warning">
                                <i class="bi bi-person-plus-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Listado de Clientes --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-2xl font-semibold mb-0">
                            <i class="bi bi-people text-primary me-2"></i>Clientes
                        </h4>
                    </div>

                    {{-- Filtros --}}
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="buscar" class="form-control"
                                       placeholder="Buscar por nombre, email o telefono..."
                                       value="{{ request('buscar') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="ordenar" class="form-select" onchange="this.form.submit()">
                                <option value="ultima_compra" {{ request('ordenar') == 'ultima_compra' ? 'selected' : '' }}>Ultima compra</option>
                                <option value="total_gastado" {{ request('ordenar') == 'total_gastado' ? 'selected' : '' }}>Total gastado</option>
                                <option value="total_compras" {{ request('ordenar') == 'total_compras' ? 'selected' : '' }}>Cantidad de compras</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Buscar
                            </button>
                        </div>
                    </form>

                    @if($clientes->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted"></i>
                            <h5 class="mt-3 text-muted">No hay clientes aun</h5>
                            <p class="text-muted">Los clientes apareceran aqui cuando realicen compras.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Contacto</th>
                                        <th class="text-center">Compras</th>
                                        <th class="text-end">Total Gastado</th>
                                        <th>Ultima Compra</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientes as $cliente)
                                        <tr class="cliente-row" onclick="window.location='{{ route('gestion-clientes.show', $cliente->email_cliente) }}'">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-3">
                                                        {{ strtoupper(substr($cliente->nombre_cliente, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $cliente->nombre_cliente }}</div>
                                                        <small class="text-muted">{{ $cliente->email_cliente }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="tel:{{ $cliente->telefono_cliente }}" class="text-decoration-none" onclick="event.stopPropagation()">
                                                    {{ $cliente->telefono_cliente }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $cliente->total_compras }}</span>
                                            </td>
                                            <td class="text-end fw-bold">
                                                ${{ number_format($cliente->total_gastado, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($cliente->ultima_compra)->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($cliente->ultima_compra)->diffForHumans() }}</small>
                                            </td>
                                            <td class="text-end" onclick="event.stopPropagation()">
                                                <a href="{{ route('gestion-clientes.show', $cliente->email_cliente) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($clientes->hasPages())
                            <div class="px-6 py-4 border-top">
                                {{ $clientes->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
