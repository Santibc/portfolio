<x-app-layout>
    <x-agromarket.page-header
        title="Historial de Dividendos"
        subtitle="Registro completo de todos tus dividendos pagados"
    />

    {{-- Cards de Resumen con Estilo Mejorado --}}
    <div class="history-stats-grid">
        <div class="history-stat-card stat-success">
            <div class="stat-icon-wrapper">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Total Historico</span>
                <span class="stat-value">${{ number_format($totalHistorico, 0, ',', '.') }}</span>
                <span class="stat-description">Acumulado de todos los tiempos</span>
            </div>
            <div class="stat-decoration"></div>
        </div>

        <div class="history-stat-card stat-primary">
            <div class="stat-icon-wrapper">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Total {{ date('Y') }}</span>
                <span class="stat-value">${{ number_format($totalAnual, 0, ',', '.') }}</span>
                <span class="stat-description">Ganancias este ano</span>
            </div>
            <div class="stat-decoration"></div>
        </div>

        <div class="history-stat-card stat-info">
            <div class="stat-icon-wrapper">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Promedio Mensual</span>
                <span class="stat-value">${{ number_format($promedioMensual, 0, ',', '.') }}</span>
                <span class="stat-description">Rendimiento promedio</span>
            </div>
            <div class="stat-decoration"></div>
        </div>
    </div>

    {{-- Seccion de Filtros --}}
    <div class="history-section filters-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-filter"></i>
                <span>Filtros de Busqueda</span>
            </div>
            <a href="{{ route('inversionista.dividends.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a Dividendos</span>
            </a>
        </div>
        <div class="section-body">
            <form action="{{ route('inversionista.dividends.history') }}" method="GET" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="proyecto_id">
                            <i class="fas fa-project-diagram"></i> Proyecto
                        </label>
                        <select name="proyecto_id" id="proyecto_id" class="form-control">
                            <option value="">Todos los proyectos</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}" {{ ($filters['proyecto_id'] ?? '') == $proyecto->id ? 'selected' : '' }}>
                                    {{ $proyecto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="fecha_desde">
                            <i class="fas fa-calendar-alt"></i> Desde
                        </label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                               value="{{ $filters['fecha_desde'] ?? '' }}">
                    </div>

                    <div class="filter-group">
                        <label for="fecha_hasta">
                            <i class="fas fa-calendar-alt"></i> Hasta
                        </label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                               value="{{ $filters['fecha_hasta'] ?? '' }}">
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('inversionista.dividends.history') }}" class="btn btn-outline">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Historial de Dividendos --}}
    <div class="history-section history-list-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-history"></i>
                <span>Historial de Pagos</span>
            </div>
            @if($dividendos->count() > 0)
                <span class="results-count">
                    <i class="fas fa-check-circle"></i>
                    {{ $dividendos->total() }} pago(s) encontrado(s)
                </span>
            @endif
        </div>
        <div class="section-body">
            @if($dividendos->count() > 0)
                {{-- Vista Desktop: Tabla --}}
                <div class="table-container desktop-only">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-hashtag"></i> Codigo
                                    </span>
                                </th>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-seedling"></i> Proyecto
                                    </span>
                                </th>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-sync-alt"></i> Periodo
                                    </span>
                                </th>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-dollar-sign"></i> Monto
                                    </span>
                                </th>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-calendar-check"></i> Fecha de Pago
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dividendos as $dividendo)
                                <tr class="history-row">
                                    <td>
                                        <span class="dividend-code">
                                            <i class="fas fa-receipt"></i>
                                            {{ $dividendo->codigo_dividendo }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="project-info">
                                            <span class="project-name">{{ $dividendo->proyecto->nombre ?? 'N/A' }}</span>
                                            <span class="project-category">{{ $dividendo->proyecto->categoria->nombre ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="period-badge">
                                            <i class="fas fa-layer-group"></i>
                                            Periodo {{ $dividendo->numero_periodo }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="amount-value">
                                            ${{ number_format($dividendo->monto, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="payment-date">
                                            <i class="fas fa-check-circle"></i>
                                            <div class="date-info">
                                                <span class="date-main">{{ $dividendo->fecha_pagada ? $dividendo->fecha_pagada->format('d/m/Y') : 'N/A' }}</span>
                                                <span class="date-time">{{ $dividendo->fecha_pagada ? $dividendo->fecha_pagada->format('H:i') : '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Vista Mobile: Cards --}}
                <div class="mobile-cards mobile-only">
                    @foreach($dividendos as $dividendo)
                        <div class="history-card">
                            <div class="card-top">
                                <span class="dividend-code-mobile">
                                    <i class="fas fa-receipt"></i>
                                    {{ $dividendo->codigo_dividendo }}
                                </span>
                                <span class="amount-badge">
                                    ${{ number_format($dividendo->monto, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="card-middle">
                                <div class="project-info-mobile">
                                    <i class="fas fa-seedling"></i>
                                    <span>{{ $dividendo->proyecto->nombre ?? 'N/A' }}</span>
                                </div>
                                <span class="period-badge-mobile">
                                    Periodo {{ $dividendo->numero_periodo }}
                                </span>
                            </div>
                            <div class="card-bottom">
                                <div class="payment-status">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Pagado el {{ $dividendo->fecha_pagada ? $dividendo->fecha_pagada->format('d/m/Y H:i') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginacion --}}
                @if($dividendos->hasPages())
                    <div class="pagination-container">
                        {{ $dividendos->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state-container">
                    <div class="empty-icon-wrapper">
                        <i class="fas fa-history"></i>
                        <div class="empty-icon-ring"></div>
                    </div>
                    <h4>Sin historial de pagos</h4>
                    <p>Aun no has recibido ningun dividendo. Los pagos apareceran aqui cuando se procesen.</p>
                    <a href="{{ route('inversionista.dividends.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Ver Dividendos Pendientes
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        /* Stats Grid - SIN animaciones */
        .history-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .history-stat-card {
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

        .history-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .stat-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-success .stat-icon-wrapper { background: #dcfce7; color: #16a34a; }
        .stat-primary .stat-icon-wrapper { background: #d1fae5; color: #2D5A27; }
        .stat-info .stat-icon-wrapper { background: #dbeafe; color: #0ea5e9; }

        .stat-content {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            flex: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
        }

        .stat-value {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-description {
            font-size: 0.75rem;
            color: #9ca3af;
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

        .stat-success .stat-decoration { background: #16a34a; }
        .stat-primary .stat-decoration { background: #2D5A27; }
        .stat-info .stat-decoration { background: #0ea5e9; }

        /* Secciones */
        .history-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .section-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .section-title i {
            color: #2D5A27;
        }

        .section-body {
            padding: 1.25rem;
        }

        /* Botones */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #4b5563;
            background: #f3f4f6;
            text-decoration: none;
            transition: background 0.15s ease;
        }

        .btn-back:hover {
            background: #e5e7eb;
            color: #2D5A27;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: #2D5A27;
            color: white;
        }

        .btn-primary:hover {
            background: #1e3d1a;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }

        .btn-outline:hover {
            border-color: #2D5A27;
            color: #2D5A27;
        }

        .results-count {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Filtros - Layout HORIZONTAL */
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group label i {
            color: #2D5A27;
            font-size: 0.75rem;
        }

        .form-control {
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: border-color 0.15s ease;
            background: white;
            width: 100%;
        }

        .form-control:focus {
            outline: none;
            border-color: #2D5A27;
            box-shadow: 0 0 0 2px rgba(45, 90, 39, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        /* Tabla Desktop */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table thead {
            background: #f9fafb;
        }

        .history-table th {
            padding: 0.875rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #4b5563;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }

        .th-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .th-content i {
            color: #2D5A27;
            font-size: 0.7rem;
        }

        .history-table td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .history-row {
            transition: background 0.15s ease;
        }

        .history-row:hover {
            background: #f9fafb;
        }

        .dividend-code {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: monospace;
            font-size: 0.8rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .dividend-code i {
            color: #2D5A27;
            font-size: 0.7rem;
        }

        .project-info {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .project-name {
            font-weight: 600;
            color: #1f2937;
        }

        .project-category {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .period-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background: #e0e7ff;
            color: #4f46e5;
            padding: 0.25rem 0.625rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .period-badge i {
            font-size: 0.65rem;
        }

        .amount-value {
            font-weight: 700;
            font-size: 1rem;
            color: #16a34a;
        }

        .payment-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #16a34a;
        }

        .payment-date i {
            font-size: 1rem;
        }

        .date-info {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .date-main {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .date-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Cards Mobile */
        .mobile-cards {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .history-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem;
            transition: border-color 0.15s ease;
        }

        .history-card:hover {
            border-color: #2D5A27;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed #e5e7eb;
        }

        .dividend-code-mobile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-family: monospace;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .dividend-code-mobile i {
            color: #2D5A27;
        }

        .amount-badge {
            background: #dcfce7;
            color: #16a34a;
            padding: 0.25rem 0.625rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .card-middle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .project-info-mobile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .project-info-mobile i {
            color: #2D5A27;
        }

        .period-badge-mobile {
            background: #e0e7ff;
            color: #4f46e5;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .card-bottom {
            display: flex;
            align-items: center;
        }

        .payment-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #16a34a;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Empty State */
        .empty-state-container {
            text-align: center;
            padding: 3rem 2rem;
        }

        .empty-icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-icon-wrapper i {
            font-size: 2rem;
            color: #9ca3af;
        }

        .empty-icon-ring {
            display: none;
        }

        .empty-state-container h4 {
            font-size: 1.25rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .empty-state-container p {
            color: #6b7280;
            max-width: 350px;
            margin: 0 auto 1.25rem;
        }

        /* Paginacion */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        /* Responsive */
        .desktop-only {
            display: block;
        }

        .mobile-only {
            display: none;
        }

        @media (max-width: 1024px) {
            .history-stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .filter-group {
                max-width: none;
            }
        }

        @media (max-width: 768px) {
            .desktop-only {
                display: none;
            }

            .mobile-only {
                display: block;
            }

            .history-stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .section-header {
                flex-direction: column;
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

            .section-body {
                padding: 1rem;
            }
        }
    </style>
    @endpush
</x-app-layout>
