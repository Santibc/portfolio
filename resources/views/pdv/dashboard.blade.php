<x-app-layout>
    @section('title', 'Punto de Venta')

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-shop me-2"></i>Punto de Venta</h4>
        </div>

        {{-- Active Session Banner --}}
        @if(isset($sesionActiva) && $sesionActiva)
            <div class="alert alert-success d-flex align-items-center justify-content-between mb-4" role="alert">
                <div>
                    <i class="bi bi-unlock-fill me-2"></i>
                    <strong>Caja abierta:</strong> {{ $sesionActiva->caja->nombre }}
                    <span class="ms-3 text-muted">Desde: {{ $sesionActiva->abierta_en->format('h:i A') }}</span>
                    <span class="ms-3 text-muted">Base: ${{ number_format($sesionActiva->monto_apertura, 2) }}</span>
                </div>
                <div>
                    <a href="{{ route('pdv.ventas.crear') }}" class="btn btn-success btn-sm me-2">
                        <i class="bi bi-cart-plus me-1"></i>Nueva Venta
                    </a>
                    <a href="{{ route('pdv.sesiones.cerrar.form', $sesionActiva->id) }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-lock me-1"></i>Cerrar Caja
                    </a>
                </div>
            </div>
        @endif

        {{-- Admin Dashboard --}}
        @if(auth()->user()->hasRole('admin'))
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold" style="color: var(--miracle-pink);">
                                ${{ number_format($totalVentasHoy ?? 0, 0) }}
                            </div>
                            <small class="text-muted">Ventas de Hoy</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold" style="color: var(--miracle-lilac);">
                                {{ $cantidadVentasHoy ?? 0 }}
                            </div>
                            <small class="text-muted">Transacciones</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold text-warning">
                                {{ $prefacturasPendientes ?? 0 }}
                            </div>
                            <small class="text-muted">Prefacturas Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold text-success">
                                {{ isset($sesionesAbiertas) ? $sesionesAbiertas->count() : 0 }}
                            </div>
                            <small class="text-muted">Cajas Abiertas</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Open sessions --}}
            @if(isset($sesionesAbiertas) && $sesionesAbiertas->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-unlock me-2"></i>Sesiones Abiertas</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Caja</th>
                                        <th>Cajero</th>
                                        <th>Apertura</th>
                                        <th>Base</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sesionesAbiertas as $sa)
                                        <tr>
                                            <td>{{ $sa->caja->nombre }}</td>
                                            <td>{{ $sa->usuario->name }}</td>
                                            <td>{{ $sa->abierta_en->format('d/m/Y h:i A') }}</td>
                                            <td>${{ number_format($sa->monto_apertura, 2) }}</td>
                                            <td>
                                                <a href="{{ route('pdv.sesiones.resumen', $sa->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Cajero Principal Dashboard (no active session) --}}
        @if(auth()->user()->hasRole('cajero_principal') && (!isset($sesionActiva) || !$sesionActiva))
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-lock display-1 text-muted mb-3"></i>
                            <h5>No tiene una caja abierta</h5>
                            <p class="text-muted">Seleccione una caja para abrir una sesion</p>
                            <div class="row g-3 justify-content-center mt-3">
                                @foreach($cajas as $c)
                                    @if($c->estaCerrada() && $c->activo)
                                        <div class="col-md-4">
                                            <a href="{{ route('pdv.sesiones.abrir.form', $c->id) }}" class="card border-0 shadow-sm text-decoration-none h-100" style="transition: transform .2s;">
                                                <div class="card-body text-center py-4">
                                                    <i class="bi bi-cash-stack display-4 mb-2" style="color: var(--miracle-pink);"></i>
                                                    <h6 class="fw-bold">{{ $c->nombre }}</h6>
                                                    <small class="text-muted">{{ $c->ubicacion->nombre ?? '' }}</small>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Cajero Principal with active session metrics --}}
        @if(auth()->user()->hasRole('cajero_principal') && isset($sesionActiva) && $sesionActiva)
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="h3 fw-bold" style="color: var(--miracle-pink);">
                                ${{ number_format($totalSesion ?? 0, 0) }}
                            </div>
                            <small class="text-muted">Total Sesion</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="h3 fw-bold" style="color: var(--miracle-lilac);">
                                {{ $ventasSesion ?? 0 }}
                            </div>
                            <small class="text-muted">Ventas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="h3 fw-bold text-warning">
                                {{ $prefacturasPendientes ?? 0 }}
                            </div>
                            <small class="text-muted">Prefacturas Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="h3 fw-bold text-danger">
                                ${{ number_format($valesSesion ?? 0, 0) }}
                            </div>
                            <small class="text-muted">Vales</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions for Cajero --}}
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <a href="{{ route('pdv.ventas.crear') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-cart-plus fs-2" style="color: var(--miracle-pink);"></i>
                            <div class="small fw-semibold mt-1">Nueva Venta</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('pdv.prefacturas.pendientes') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-receipt fs-2" style="color: var(--miracle-lilac);"></i>
                            <div class="small fw-semibold mt-1">Prefacturas</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('pdv.vales.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-ticket-perforated fs-2 text-warning"></i>
                            <div class="small fw-semibold mt-1">Vales</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('pdv.ventas.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-list-ul fs-2 text-info"></i>
                            <div class="small fw-semibold mt-1">Historial</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('traslados.form') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-arrow-left-right fs-2 text-primary"></i>
                            <div class="small fw-semibold mt-1">Traslado</div>
                        </div>
                    </a>
                </div>
            </div>
        @endif

        {{-- Auxiliar de Venta / Vendedor Dashboard --}}
        @if(auth()->user()->hasRole(['auxiliar_venta', 'vendedor']) && !auth()->user()->hasRole(['admin', 'cajero_principal']))
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <a href="{{ route('pdv.prefacturas.crear') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-plus-circle display-4" style="color: var(--miracle-pink);"></i>
                            <h5 class="fw-bold mt-3">Crear Prefactura</h5>
                            <p class="text-muted small">Prepare una venta para que la cajera la procese</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('pdv.prefacturas.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-list-check display-4" style="color: var(--miracle-lilac);"></i>
                            <h5 class="fw-bold mt-3">Mis Prefacturas</h5>
                            <p class="text-muted small">Vea el estado de sus prefacturas</p>
                        </div>
                    </a>
                </div>
            </div>

            @if(isset($misPrefacturas) && $misPrefacturas->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Mis Ultimas Prefacturas</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Numero</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($misPrefacturas as $pf)
                                        <tr>
                                            <td>{{ $pf->numero_prefactura }}</td>
                                            <td>{{ $pf->nombre_cliente_display }}</td>
                                            <td>${{ number_format($pf->total, 2) }}</td>
                                            <td>{!! $pf->estado_badge !!}</td>
                                            <td>{{ $pf->created_at->format('d/m/Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
