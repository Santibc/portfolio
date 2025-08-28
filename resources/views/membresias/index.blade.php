<x-app-layout>
  <x-slot name="header">Planes de Membresía</x-slot>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h1 class="page-title">Planes de Membresía</h1>
                <p class="text-muted">Elige el plan que mejor se adapte a tu negocio</p>
            </div>

            @if($membresiaActiva)
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Plan actual: {{ $membresiaActiva->plan->nombre }}</strong>
                        @if($membresiaActiva->fecha_fin)
                        <br><small>Válido hasta: {{ $membresiaActiva->fecha_fin->format('d/m/Y') }} ({{ $membresiaActiva->diasRestantes() }} días restantes)</small>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Estado actual de la empresa -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="mb-1">{{ $empresa->productos()->count() }}/{{ $empresa->limite_productos }}</h3>
                            <p class="text-muted mb-0">Productos creados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="mb-1">{{ $empresa->porcentaje_comision }}% + ${{ number_format($empresa->cargo_fijo_comision, 0) }}</h3>
                            <p class="text-muted mb-0">Comisión actual</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="mb-1 text-{{ $empresa->planMembresia->esGratuito() ? 'warning' : 'success' }}">
                                {{ $empresa->planMembresia->nombre }}
                            </h3>
                            <p class="text-muted mb-0">Tu plan actual</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Planes disponibles -->
            <div class="row">
                @foreach($planes as $plan)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 {{ $empresa->plan_membresia_id == $plan->id ? 'border-primary' : '' }}">
                        @if($empresa->plan_membresia_id == $plan->id)
                        <div class="ribbon ribbon-top bg-primary">
                            <span>Plan Actual</span>
                        </div>
                        @endif
                        
                        <div class="card-header text-center">
                            <h3 class="card-title mb-1">{{ $plan->nombre }}</h3>
                            <div class="text-primary">
                                @if($plan->precio == 0)
                                <h2 class="mb-0">GRATIS</h2>
                                @else
                                <h2 class="mb-0">${{ number_format($plan->precio, 0) }}</h2>
                                <small class="text-muted">/mes</small>
                                @endif
                            </div>
                            @if($plan->limite_transacciones)
                            <small class="text-muted">Hasta {{ $plan->limite_transacciones }} transacciones/mes</small>
                            @endif
                        </div>
                        
                        <div class="card-body">
                            <ul class="list-unstyled mb-4">
                                @if($plan->caracteristicas)
                                    @foreach($plan->caracteristicas as $caracteristica)
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        {{ $caracteristica }}
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                            
                            <div class="commission-box bg-light p-3 rounded mb-3">
                                <strong>{{ $plan->porcentaje_comision }}% + ${{ number_format($plan->comision_fija, 0) }}</strong>
                                <br>
                                <small class="text-muted">Por transacción exitosa</small>
                            </div>
                        </div>
                        
                        <div class="card-footer text-center">
                            @if($empresa->plan_membresia_id == $plan->id)
                                <button class="btn btn-secondary" disabled>
                                    <i class="bi bi-check"></i> Plan Actual
                                </button>
                            @else
                                <a href="{{ route('membresias.show', $plan->slug) }}" class="btn btn-primary">
                                    Cambiar a este Plan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">¿Necesitas ayuda eligiendo un plan?</h5>
                            <p class="card-text">
                                Cada plan está diseñado para diferentes etapas de crecimiento. 
                                Si tienes dudas sobre cuál es el mejor para ti, contáctanos.
                            </p>
                            <a href="{{ route('membresias.historial') }}" class="btn btn-outline-primary">
                                <i class="bi bi-clock-history"></i> Ver historial de membresías
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>