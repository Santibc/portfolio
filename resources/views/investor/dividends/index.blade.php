<x-app-layout>
    <x-agromarket.page-header
        title="Mis Dividendos"
        subtitle="Consulta tus dividendos y rendimientos de inversiones"
    >
        <x-slot:breadcrumb>
            <a href="{{ route('inversionista.investments.index') }}">Mis Inversiones</a>
            <span>/</span>
            <span>Dividendos</span>
        </x-slot:breadcrumb>
    </x-agromarket.page-header>

    {{-- Cards de Resumen con animacion --}}
    <div class="stats-grid">
        <div class="stat-card success">
            <div class="stat-icon">
                <i class="{{ $summary['total_recibido']['icono'] }}"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">{{ $summary['total_recibido']['titulo'] }}</span>
                <span class="stat-value">{{ $summary['total_recibido']['formateado'] }}</span>
            </div>
            <div class="stat-decoration"></div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="{{ $summary['pendientes']['icono'] }}"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">{{ $summary['pendientes']['titulo'] }}</span>
                <span class="stat-value">{{ $summary['pendientes']['formateado'] }}</span>
                @if($summary['pendientes']['count'] > 0)
                    <span class="stat-count">{{ $summary['pendientes']['count'] }} pendientes</span>
                @endif
            </div>
            <div class="stat-decoration"></div>
        </div>

        <div class="stat-card info">
            <div class="stat-icon">
                <i class="{{ $summary['proximo_dividendo']['icono'] }}"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">{{ $summary['proximo_dividendo']['titulo'] }}</span>
                <span class="stat-value">{{ $summary['proximo_dividendo']['formateado'] }}</span>
                @if($summary['proximo_dividendo']['fecha'] !== '-')
                    <span class="stat-date">{{ $summary['proximo_dividendo']['fecha'] }}</span>
                @endif
            </div>
            <div class="stat-decoration"></div>
        </div>

        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="{{ $summary['este_mes']['icono'] }}"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">{{ $summary['este_mes']['titulo'] }}</span>
                <span class="stat-value">{{ $summary['este_mes']['formateado'] }}</span>
            </div>
            <div class="stat-decoration"></div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="filters-section">
        <div class="filters-header">
            <div class="filters-title">
                <i class="fas fa-filter"></i>
                <span>Filtros</span>
            </div>
        </div>
        <form action="{{ route('inversionista.dividends.index') }}" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $key => $label)
                            <option value="{{ $key }}" {{ ($filters['estado'] ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="proyecto_id">Proyecto</label>
                    <select name="proyecto_id" id="proyecto_id" class="form-select">
                        <option value="">Todos los proyectos</option>
                        @foreach($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}" {{ ($filters['proyecto_id'] ?? '') == $proyecto->id ? 'selected' : '' }}>
                                {{ $proyecto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="fecha_desde">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-input"
                           value="{{ $filters['fecha_desde'] ?? '' }}">
                </div>

                <div class="filter-group">
                    <label for="fecha_hasta">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-input"
                           value="{{ $filters['fecha_hasta'] ?? '' }}">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('inversionista.dividends.index') }}" class="btn btn-ghost">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Lista de Dividendos --}}
    <div class="dividends-section">
        <div class="section-header">
            <h3><i class="fas fa-coins"></i> Mis Dividendos</h3>
            <div class="section-actions">
                <span class="results-count">{{ $dividendos->total() }} resultado(s)</span>
                <a href="{{ route('inversionista.dividends.history') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-history"></i> Ver Historial
                </a>
            </div>
        </div>

        @if($dividendos->count() > 0)
            <div class="dividends-list">
                @foreach($dividendos as $dividendo)
                    <div class="dividend-card">
                        <div class="dividend-main">
                            <div class="dividend-project">
                                <div class="project-icon">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div class="project-info">
                                    <h4>{{ $dividendo->proyecto->nombre ?? 'Proyecto' }}</h4>
                                    <span class="dividend-code">{{ $dividendo->codigo_dividendo }}</span>
                                </div>
                            </div>

                            <div class="dividend-details">
                                <div class="detail-item">
                                    <span class="detail-label">Periodo</span>
                                    <span class="detail-value period-badge">{{ $dividendo->numero_periodo }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Monto</span>
                                    <span class="detail-value amount">${{ number_format($dividendo->monto, 0, ',', '.') }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Fecha Programada</span>
                                    <span class="detail-value">
                                        <i class="fas fa-calendar"></i>
                                        {{ $dividendo->fecha_programada ? $dividendo->fecha_programada->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="dividend-status">
                            @php
                                $estadoConfig = [
                                    'programado' => ['class' => 'status-programado', 'icon' => 'fas fa-clock', 'text' => 'Programado'],
                                    'pagado' => ['class' => 'status-pagado', 'icon' => 'fas fa-check-circle', 'text' => 'Pagado'],
                                    'atrasado' => ['class' => 'status-atrasado', 'icon' => 'fas fa-exclamation-triangle', 'text' => 'Atrasado'],
                                    'cancelado' => ['class' => 'status-cancelado', 'icon' => 'fas fa-times-circle', 'text' => 'Cancelado'],
                                ];
                                $config = $estadoConfig[$dividendo->estado] ?? $estadoConfig['programado'];
                            @endphp
                            <div class="status-badge {{ $config['class'] }}">
                                <i class="{{ $config['icon'] }}"></i>
                                <span>{{ $config['text'] }}</span>
                            </div>
                            @if($dividendo->estado === 'pagado' && $dividendo->fecha_pagada)
                                <span class="paid-date">
                                    <i class="fas fa-check"></i> {{ $dividendo->fecha_pagada->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginacion --}}
            <div class="pagination-wrapper">
                {{ $dividendos->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <h4>No tienes dividendos</h4>
                <p>Los dividendos se generan automaticamente cuando realizas inversiones en proyectos.</p>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary">
                    <i class="fas fa-seedling"></i> Explorar Proyectos
                </a>
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        /* Stats Grid - SIN animaciones pesadas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .stat-decoration {
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            opacity: 0.1;
        }

        .stat-card.success .stat-decoration { background: #16a34a; }
        .stat-card.warning .stat-decoration { background: #f59e0b; }
        .stat-card.info .stat-decoration { background: #3b82f6; }
        .stat-card.primary .stat-decoration { background: #2D5A27; }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-card.success .stat-icon { background: #dcfce7; color: #16a34a; }
        .stat-card.warning .stat-icon { background: #fef3c7; color: #f59e0b; }
        .stat-card.info .stat-icon { background: #dbeafe; color: #3b82f6; }
        .stat-card.primary .stat-icon { background: #d1fae5; color: #2D5A27; }

        .stat-content {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            flex: 1;
            min-width: 0;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-count, .stat-date {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Filters Section - Layout HORIZONTAL fijo */
        .filters-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .filters-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
        }

        .filters-title i {
            color: #2D5A27;
        }

        /* Grid de filtros en UNA SOLA FILA */
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
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
        }

        .form-select, .form-input {
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: border-color 0.15s ease;
            background: white;
            width: 100%;
        }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #2D5A27;
            box-shadow: 0 0 0 2px rgba(45, 90, 39, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        /* Dividends Section */
        .dividends-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: linear-gradient(to right, #f9fafb, #ffffff);
            border-bottom: 1px solid #e5e7eb;
        }

        .section-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header h3 i {
            color: #f59e0b;
        }

        .section-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .results-count {
            font-size: 0.85rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
        }

        /* Dividend Cards */
        .dividends-list {
            padding: 1rem;
        }

        .dividend-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            margin-bottom: 0.75rem;
            background: linear-gradient(to right, #fafafa, #ffffff);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .dividend-card:last-child {
            margin-bottom: 0;
        }

        .dividend-card:hover {
            border-color: #2D5A27;
            box-shadow: 0 4px 20px rgba(45, 90, 39, 0.08);
            transform: translateX(4px);
        }

        .dividend-main {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex: 1;
        }

        .dividend-project {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 250px;
        }

        .project-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2D5A27 0%, #4a9c40 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .project-info h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 0.25rem 0;
        }

        .dividend-code {
            font-size: 0.8rem;
            color: #6b7280;
            font-family: monospace;
            background: #f3f4f6;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
        }

        .dividend-details {
            display: flex;
            gap: 2rem;
            flex: 1;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .detail-label {
            font-size: 0.75rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-value i {
            color: #9ca3af;
            font-size: 0.85rem;
        }

        .detail-value.amount {
            color: #2D5A27;
            font-size: 1.1rem;
        }

        .period-badge {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4f46e5;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        /* Status */
        .dividend-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-programado {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-pagado {
            background: #dcfce7;
            color: #166534;
        }

        .status-atrasado {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelado {
            background: #fee2e2;
            color: #991b1b;
        }

        .paid-date {
            font-size: 0.8rem;
            color: #16a34a;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
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
            background: linear-gradient(135deg, #2D5A27 0%, #3d7a35 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.3);
        }

        .btn-outline-primary {
            background: transparent;
            border: 2px solid #2D5A27;
            color: #2D5A27;
        }

        .btn-outline-primary:hover {
            background: #2D5A27;
            color: white;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .btn-ghost {
            background: transparent;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .btn-ghost:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: #9ca3af;
        }

        .empty-state h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #6b7280;
            max-width: 400px;
            margin: 0 auto 1.5rem;
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper nav > div {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-wrapper nav > div:first-child {
            display: none;
        }

        .pagination-wrapper nav > div > div,
        .pagination-wrapper nav > div > span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .pagination-wrapper a,
        .pagination-wrapper span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .pagination-wrapper a {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .pagination-wrapper a:hover {
            background: #2D5A27;
            color: white;
            border-color: #2D5A27;
        }

        .pagination-wrapper span[aria-current="page"] span,
        .pagination-wrapper .bg-blue-50,
        .pagination-wrapper [aria-current="page"] {
            background: #2D5A27 !important;
            color: white !important;
            border: 1px solid #2D5A27 !important;
        }

        .pagination-wrapper span[aria-disabled="true"],
        .pagination-wrapper .cursor-default {
            background: #f3f4f6;
            color: #9ca3af;
            border: 1px solid #e5e7eb;
            cursor: not-allowed;
        }

        .pagination-wrapper svg {
            width: 18px;
            height: 18px;
        }

        .pagination-wrapper p {
            font-size: 0.85rem;
            color: #6b7280;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .dividend-main {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .dividend-project {
                min-width: auto;
            }

            .dividend-details {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .filter-group {
                max-width: none;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .dividend-card {
                flex-direction: column;
                gap: 1rem;
            }

            .dividend-status {
                width: 100%;
                flex-direction: row;
                justify-content: space-between;
                padding-top: 1rem;
                border-top: 1px dashed #e5e7eb;
            }

            .filters-header {
                flex-direction: column;
                gap: 0.75rem;
                align-items: flex-start;
            }

            .filters-grid {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
                max-width: none;
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
