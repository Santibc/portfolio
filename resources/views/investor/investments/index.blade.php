<x-app-layout>
    <x-agromarket.page-header
        title="Mis Inversiones"
        subtitle="Consulta el estado de todas tus inversiones"
    />

    {{-- Cards de Resumen --}}
    <div class="stats-grid">
        <x-agromarket.stat-card
            :title="$summary['total_invertido']['titulo']"
            :value="$summary['total_invertido']['formateado']"
            :icon="$summary['total_invertido']['icono']"
            :color="$summary['total_invertido']['color']"
        />

        <x-agromarket.stat-card
            :title="$summary['inversiones_activas']['titulo']"
            :value="$summary['inversiones_activas']['formateado']"
            :icon="$summary['inversiones_activas']['icono']"
            :color="$summary['inversiones_activas']['color']"
        />

        <x-agromarket.stat-card
            :title="$summary['ganancias_acumuladas']['titulo']"
            :value="$summary['ganancias_acumuladas']['formateado']"
            :icon="$summary['ganancias_acumuladas']['icono']"
            :color="$summary['ganancias_acumuladas']['color']"
        />

        <x-agromarket.stat-card
            :title="$summary['proximo_dividendo']['titulo']"
            :value="$summary['proximo_dividendo']['formateado']"
            :icon="$summary['proximo_dividendo']['icono']"
            :color="$summary['proximo_dividendo']['color']"
        />
    </div>

    {{-- Filtros --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filtros
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('inversionista.investments.index') }}" method="GET" class="filters-form">
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label for="estado">Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="activa" {{ ($filters['estado'] ?? '') == 'activa' ? 'selected' : '' }}>Activa</option>
                                    <option value="pendiente_pago" {{ ($filters['estado'] ?? '') == 'pendiente_pago' ? 'selected' : '' }}>Pendiente de Pago</option>
                                    <option value="en_trading" {{ ($filters['estado'] ?? '') == 'en_trading' ? 'selected' : '' }}>En Venta</option>
                                    <option value="vencida" {{ ($filters['estado'] ?? '') == 'vencida' ? 'selected' : '' }}>Vencida</option>
                                    <option value="vendida" {{ ($filters['estado'] ?? '') == 'vendida' ? 'selected' : '' }}>Vendida</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="proyecto_id">Proyecto</label>
                                <select name="proyecto_id" id="proyecto_id" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach($proyectosConInversiones as $proyecto)
                                        <option value="{{ $proyecto->id }}" {{ ($filters['proyecto_id'] ?? '') == $proyecto->id ? 'selected' : '' }}>
                                            {{ $proyecto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="fecha_desde">Desde</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                                       value="{{ $filters['fecha_desde'] ?? '' }}">
                            </div>

                            <div class="filter-group">
                                <label for="fecha_hasta">Hasta</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                                       value="{{ $filters['fecha_hasta'] ?? '' }}">
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('inversionista.investments.index') }}" class="btn btn-outline">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Listado de Inversiones --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Mis Inversiones
                    </h3>
                    <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nueva Inversión
                    </a>
                </div>
                <div class="card-body">
                    @if($investments->count() > 0)
                        <div class="investments-list">
                            @foreach($investments as $investment)
                                <div class="investment-item">
                                    <div class="investment-image">
                                        @if($investment->proyecto->imagenPrincipal())
                                            <img src="{{ asset($investment->proyecto->imagenPrincipal()->ruta) }}" alt="{{ $investment->proyecto->nombre }}">
                                        @else
                                            <div class="no-image">
                                                <i class="fas fa-seedling"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="investment-info">
                                        <div class="investment-header">
                                            <span class="investment-code">{{ $investment->codigo_inversion }}</span>
                                            <x-agromarket.badge
                                                :color="\App\Services\Investment\InvestmentService::getInvestmentStateColor($investment->estado)"
                                                :text="\App\Services\Investment\InvestmentService::getInvestmentStateLabel($investment->estado)"
                                            />
                                        </div>
                                        <h4 class="investment-project">{{ $investment->proyecto->nombre }}</h4>
                                        <div class="investment-meta">
                                            <span><i class="fas fa-tag"></i> {{ $investment->proyecto->categoria->nombre ?? 'Sin categoría' }}</span>
                                            <span><i class="fas fa-calendar"></i> {{ $investment->fecha_inversion->format('d/m/Y') }}</span>
                                            <span><i class="fas fa-chart-line"></i> ROI {{ $investment->proyecto->roi_anual }}%</span>
                                        </div>
                                    </div>

                                    <div class="investment-amounts">
                                        <div class="amount-item">
                                            <span class="amount-label">Invertido</span>
                                            <span class="amount-value">${{ number_format($investment->monto_invertido, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="amount-item">
                                            <span class="amount-label">Valor Actual</span>
                                            <span class="amount-value highlight">${{ number_format($investment->valor_actual, 0, ',', '.') }}</span>
                                        </div>
                                        @if($investment->ganancia_acumulada > 0)
                                        <div class="amount-item">
                                            <span class="amount-label">Ganancia</span>
                                            <span class="amount-value success">+${{ number_format($investment->ganancia_acumulada, 0, ',', '.') }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="investment-actions">
                                        <a href="{{ route('inversionista.investments.show', $investment) }}" class="btn btn-outline">
                                            <i class="fas fa-eye"></i> Ver Detalle
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Paginación --}}
                        <div class="pagination-wrapper mt-4">
                            {{ $investments->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-chart-pie fa-3x"></i>
                            <h4>Sin inversiones</h4>
                            <p class="text-muted">Aún no has realizado ninguna inversión. Explora nuestro catálogo de proyectos agrícolas.</p>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-seedling"></i> Explorar Proyectos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 1rem;
            overflow: visible;
        }

        .stats-grid .summary-card {
            min-width: 0;
            overflow: hidden;
        }

        .stats-grid .summary-card .card-value {
            font-size: clamp(1.25rem, 3vw, 2rem);
            word-break: break-word;
            overflow-wrap: break-word;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .dashboard-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .dashboard-col-12 {
            flex: 1;
            width: 100%;
        }

        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(to right, #f9fafb, #ffffff);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-title i {
            color: #2D5A27;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Filters */
        .filters-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
            min-width: 160px;
            flex: 1;
            max-width: 220px;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2D5A27;
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
            padding-top: 0.5rem;
        }

        /* Investment List */
        .investments-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .investment-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: linear-gradient(to right, #f9fafb, #ffffff);
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .investment-item:hover {
            border-color: #2D5A27;
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.1);
            transform: translateY(-2px);
        }

        .investment-image {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .investment-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .investment-image .no-image {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #9ca3af;
        }

        .investment-info {
            flex: 1;
            min-width: 0;
        }

        .investment-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .investment-code {
            font-size: 0.8rem;
            color: #6b7280;
            font-family: monospace;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .investment-project {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 0.5rem 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .investment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .investment-meta span {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .investment-meta i {
            color: #9ca3af;
        }

        .investment-amounts {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            min-width: 150px;
        }

        .amount-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .amount-label {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .amount-value {
            font-weight: 600;
            color: #1f2937;
        }

        .amount-value.highlight {
            color: #2D5A27;
        }

        .amount-value.success {
            color: #16a34a;
        }

        .investment-actions {
            flex-shrink: 0;
        }

        /* Buttons */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #2D5A27;
            color: white;
        }

        .btn-primary:hover {
            background: #1e3d1a;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }

        .btn-outline:hover {
            border-color: #2D5A27;
            color: #2D5A27;
            background: rgba(45, 90, 39, 0.05);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-sm i,
        .btn i.fas,
        .btn i.fa-plus {
            font-size: 0.85rem;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: auto;
            height: auto;
            margin-right: 0.25rem;
        }

        .card-header .btn i {
            flex-shrink: 0;
        }

        .card-header .btn-primary i {
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(to bottom, #f9fafb, #ffffff);
            border-radius: 12px;
        }

        .empty-state i {
            color: #9ca3af;
            margin-bottom: 1.5rem;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }

        .empty-state p {
            max-width: 400px;
            margin: 0 auto;
        }

        .text-muted {
            color: #6b7280;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .investment-item {
                flex-wrap: wrap;
            }

            .investment-amounts {
                width: 100%;
                flex-direction: row;
                justify-content: space-around;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #e5e7eb;
            }

            .amount-item {
                flex-direction: column;
                align-items: center;
                gap: 0.25rem;
            }
        }

        @media (max-width: 768px) {
            .investment-image {
                width: 80px;
                height: 80px;
            }

            .investment-actions {
                width: 100%;
                margin-top: 1rem;
            }

            .investment-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .filters-grid {
                flex-direction: column;
            }

            .filter-group {
                max-width: 100%;
                width: 100%;
            }

            .filter-actions {
                flex-direction: column;
                width: 100%;
            }

            .filter-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    @endpush
</x-app-layout>
