<x-app-layout>
    <x-agromarket.page-header
        title="Gestion de Dividendos"
        subtitle="Panel de administracion para procesar y gestionar pagos de dividendos"
    />

    {{-- Cards de Resumen con Estilo Premium --}}
    <div class="admin-stats-grid">
        <div class="admin-stat-card stat-warning">
            <div class="stat-icon-wrapper">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Pendientes Hoy</span>
                <span class="stat-value">{{ $stats['pendientes_hoy'] }}</span>
                <span class="stat-hint">Requieren procesamiento</span>
            </div>
            <div class="stat-indicator">
                @if($stats['pendientes_hoy'] > 0)
                    <span class="pulse-indicator warning"></span>
                @endif
            </div>
        </div>

        <div class="admin-stat-card stat-danger">
            <div class="stat-icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Atrasados</span>
                <span class="stat-value">{{ $stats['atrasados'] }}</span>
                <span class="stat-hint">Requieren atencion</span>
            </div>
            <div class="stat-indicator">
                @if($stats['atrasados'] > 0)
                    <span class="pulse-indicator danger"></span>
                @endif
            </div>
        </div>

        <div class="admin-stat-card stat-success">
            <div class="stat-icon-wrapper">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Pagados Este Mes</span>
                <span class="stat-value">${{ number_format($stats['pagados_mes'], 0, ',', '.') }}</span>
                <span class="stat-hint">Total procesado</span>
            </div>
        </div>

        <div class="admin-stat-card stat-info">
            <div class="stat-icon-wrapper">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Proximos 7 Dias</span>
                <span class="stat-value">{{ $stats['proximos_7_dias'] }}</span>
                <span class="stat-hint">En cola de pago</span>
            </div>
        </div>
    </div>

    {{-- Panel de Acciones Rapidas --}}
    <div class="admin-section actions-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-bolt"></i>
                <span>Acciones Rapidas</span>
            </div>
        </div>
        <div class="section-body actions-body">
            <button type="button" class="action-card action-success" id="btnProcesarTodos" onclick="procesarTodos()">
                <div class="action-icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="action-content">
                    <span class="action-title">Procesar Todos</span>
                    <span class="action-desc">Pagar dividendos pendientes de hoy</span>
                </div>
                <i class="fas fa-chevron-right action-arrow"></i>
            </button>

            <button type="button" class="action-card action-warning" id="btnMarcarAtrasados" onclick="marcarAtrasados()">
                <div class="action-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="action-content">
                    <span class="action-title">Marcar Atrasados</span>
                    <span class="action-desc">Actualizar estado de dividendos vencidos</span>
                </div>
                <i class="fas fa-chevron-right action-arrow"></i>
            </button>
        </div>
    </div>

    {{-- Seccion de Filtros --}}
    <div class="admin-section filters-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-filter"></i>
                <span>Filtros de Busqueda</span>
            </div>
        </div>
        <div class="section-body">
            <form action="{{ route('admin.dividends.index') }}" method="GET" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="estado">
                            <i class="fas fa-toggle-on"></i> Estado
                        </label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Todos los estados</option>
                            @foreach($estados as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['estado'] ?? '') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="proyecto_id">
                            <i class="fas fa-seedling"></i> Proyecto
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
                        <a href="{{ route('admin.dividends.index') }}" class="btn btn-outline">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Listado de Dividendos --}}
    <div class="admin-section dividends-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-coins"></i>
                <span>Lista de Dividendos</span>
            </div>
            <span class="results-badge">
                <i class="fas fa-database"></i>
                {{ $dividendos->count() }} registro(s)
            </span>
        </div>
        <div class="section-body">
            @if($dividendos->count() > 0)
                {{-- Vista Desktop: Tabla con DataTables --}}
                <div class="table-container desktop-view">
                    <table class="admin-table" id="dividendos-table">
                        <thead>
                            <tr>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-hashtag"></i> Codigo
                                    </span>
                                </th>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-user"></i> Usuario
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
                                        <i class="fas fa-calendar"></i> Programado
                                    </span>
                                </th>
                                <th>
                                    <span class="th-content">
                                        <i class="fas fa-info-circle"></i> Estado
                                    </span>
                                </th>
                                <th class="actions-col">
                                    <span class="th-content">
                                        <i class="fas fa-cog"></i> Acciones
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dividendos as $index => $dividendo)
                                <tr id="row-{{ $dividendo->id }}" class="dividend-table-row">
                                    <td>
                                        <span class="dividend-code">
                                            <i class="fas fa-receipt"></i>
                                            {{ $dividendo->codigo_dividendo }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($dividendo->usuario->name ?? 'N', 0, 2)) }}
                                            </div>
                                            <div class="user-info">
                                                <span class="user-name">{{ $dividendo->usuario->name ?? 'N/A' }}</span>
                                                <span class="user-email">{{ $dividendo->usuario->email ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="project-cell">
                                            <span class="project-name">{{ $dividendo->proyecto->nombre ?? 'N/A' }}</span>
                                            <span class="project-code">{{ $dividendo->proyecto->codigo ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="period-badge">
                                            <i class="fas fa-layer-group"></i>
                                            {{ $dividendo->numero_periodo }}
                                        </span>
                                    </td>
                                    <td data-order="{{ $dividendo->monto }}">
                                        <span class="amount-value">${{ number_format($dividendo->monto, 0, ',', '.') }}</span>
                                    </td>
                                    <td data-order="{{ $dividendo->fecha_programada ? $dividendo->fecha_programada->format('Y-m-d') : '' }}">
                                        <span class="date-cell">
                                            <i class="fas fa-calendar-day"></i>
                                            {{ $dividendo->fecha_programada ? $dividendo->fecha_programada->format('d/m/Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $estadoConfig = [
                                                'programado' => ['class' => 'info', 'icon' => 'clock'],
                                                'pagado' => ['class' => 'success', 'icon' => 'check-circle'],
                                                'atrasado' => ['class' => 'warning', 'icon' => 'exclamation-circle'],
                                                'cancelado' => ['class' => 'danger', 'icon' => 'times-circle'],
                                            ];
                                            $config = $estadoConfig[$dividendo->estado] ?? ['class' => 'secondary', 'icon' => 'question'];
                                        @endphp
                                        <span class="status-badge status-{{ $config['class'] }}" id="estado-{{ $dividendo->id }}">
                                            <i class="fas fa-{{ $config['icon'] }}"></i>
                                            {{ ucfirst($dividendo->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($dividendo->estado === 'programado' || $dividendo->estado === 'atrasado')
                                                <button type="button" class="action-btn action-btn-success" onclick="pagarDividendo({{ $dividendo->id }})" title="Pagar Dividendo">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="action-btn action-btn-danger" onclick="cancelarDividendo({{ $dividendo->id }})" title="Cancelar Dividendo">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @elseif($dividendo->estado === 'pagado')
                                                <div class="paid-status">
                                                    <i class="fas fa-check-double"></i>
                                                    <span>{{ $dividendo->fecha_pagada ? $dividendo->fecha_pagada->format('d/m/Y') : '' }}</span>
                                                </div>
                                            @else
                                                <span class="no-actions">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Vista Mobile: Cards --}}
                <div class="mobile-cards mobile-view">
                    @foreach($dividendos as $index => $dividendo)
                        @php
                            $estadoConfig = [
                                'programado' => ['class' => 'info', 'icon' => 'clock'],
                                'pagado' => ['class' => 'success', 'icon' => 'check-circle'],
                                'atrasado' => ['class' => 'warning', 'icon' => 'exclamation-circle'],
                                'cancelado' => ['class' => 'danger', 'icon' => 'times-circle'],
                            ];
                            $config = $estadoConfig[$dividendo->estado] ?? ['class' => 'secondary', 'icon' => 'question'];
                        @endphp
                        <div class="dividend-card">
                            <div class="card-header-mobile">
                                <span class="dividend-code-mobile">
                                    <i class="fas fa-receipt"></i>
                                    {{ $dividendo->codigo_dividendo }}
                                </span>
                                <span class="status-badge status-{{ $config['class'] }}">
                                    <i class="fas fa-{{ $config['icon'] }}"></i>
                                    {{ ucfirst($dividendo->estado) }}
                                </span>
                            </div>

                            <div class="card-body-mobile">
                                <div class="info-row">
                                    <div class="user-cell-mobile">
                                        <div class="user-avatar-small">
                                            {{ strtoupper(substr($dividendo->usuario->name ?? 'N', 0, 2)) }}
                                        </div>
                                        <span>{{ $dividendo->usuario->name ?? 'N/A' }}</span>
                                    </div>
                                    <span class="amount-badge">
                                        ${{ number_format($dividendo->monto, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <div class="project-mobile">
                                        <i class="fas fa-seedling"></i>
                                        <span>{{ $dividendo->proyecto->nombre ?? 'N/A' }}</span>
                                    </div>
                                    <span class="period-badge-mobile">
                                        Periodo {{ $dividendo->numero_periodo }}
                                    </span>
                                </div>

                                <div class="info-row date-row">
                                    <span class="scheduled-date">
                                        <i class="fas fa-calendar"></i>
                                        Programado: {{ $dividendo->fecha_programada ? $dividendo->fecha_programada->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            @if($dividendo->estado === 'programado' || $dividendo->estado === 'atrasado')
                                <div class="card-actions-mobile">
                                    <button type="button" class="btn-mobile btn-mobile-success" onclick="pagarDividendo({{ $dividendo->id }})">
                                        <i class="fas fa-check"></i> Pagar
                                    </button>
                                    <button type="button" class="btn-mobile btn-mobile-danger" onclick="cancelarDividendo({{ $dividendo->id }})">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            @elseif($dividendo->estado === 'pagado')
                                <div class="card-paid-status">
                                    <i class="fas fa-check-double"></i>
                                    Pagado el {{ $dividendo->fecha_pagada ? $dividendo->fecha_pagada->format('d/m/Y H:i') : '' }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Paginacion manejada por DataTables --}}
            @else
                <div class="empty-state-container">
                    <div class="empty-icon-wrapper">
                        <i class="fas fa-coins"></i>
                        <div class="empty-icon-ring"></div>
                    </div>
                    <h4>No hay dividendos</h4>
                    <p>No se encontraron dividendos con los filtros aplicados. Intenta ajustar los criterios de busqueda.</p>
                    <a href="{{ route('admin.dividends.index') }}" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Limpiar Filtros
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        /* ==================== Variables ==================== */
        :root {
            --primary-green: #2D5A27;
            --primary-green-light: #4A7C59;
            --primary-green-dark: #1e3d1a;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
        }

        /* ==================== Stats Grid ==================== */
        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .admin-stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            position: relative;
            overflow: hidden;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border-left: 4px solid transparent;
        }

        .admin-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-warning { border-left-color: var(--warning-color); }
        .stat-danger { border-left-color: var(--danger-color); }
        .stat-success { border-left-color: var(--success-color); }
        .stat-info { border-left-color: var(--info-color); }

        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-warning .stat-icon-wrapper {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: var(--warning-color);
        }

        .stat-danger .stat-icon-wrapper {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: var(--danger-color);
        }

        .stat-success .stat-icon-wrapper {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: var(--success-color);
        }

        .stat-info .stat-icon-wrapper {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: var(--info-color);
        }

        .stat-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            flex: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .stat-hint {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .stat-indicator {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        .pulse-indicator {
            display: block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            animation: pulse-ring 1.5s ease-in-out infinite;
        }

        .pulse-indicator.warning {
            background: var(--warning-color);
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
        }

        .pulse-indicator.danger {
            background: var(--danger-color);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
        }

        /* ==================== Secciones ==================== */
        .admin-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .section-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(to right, #f9fafb, #ffffff);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .section-title i {
            color: var(--primary-green);
        }

        .section-body {
            padding: 1.5rem;
        }

        /* ==================== Acciones Rapidas ==================== */
        .actions-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .action-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
            width: 100%;
        }

        .action-card.action-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .action-card.action-success:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.05) 100%);
            border-color: var(--success-color);
            transform: translateX(4px);
        }

        .action-card.action-warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(245, 158, 11, 0.02) 100%);
            border-color: rgba(245, 158, 11, 0.2);
        }

        .action-card.action-warning:hover {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.05) 100%);
            border-color: var(--warning-color);
            transform: translateX(4px);
        }

        .action-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .action-success .action-icon {
            background: var(--success-color);
            color: white;
        }

        .action-warning .action-icon {
            background: var(--warning-color);
            color: white;
        }

        .action-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .action-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
        }

        .action-desc {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .action-arrow {
            color: #9ca3af;
            transition: transform 0.2s ease;
        }

        .action-card:hover .action-arrow {
            transform: translateX(4px);
            color: #4b5563;
        }

        /* ==================== Filtros ==================== */
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group label i {
            color: var(--primary-green);
            font-size: 0.8rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
            background: white;
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
        }

        .results-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: var(--primary-green);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* ==================== Botones ==================== */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
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
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(45, 90, 39, 0.35);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #e5e7eb;
            color: #4b5563;
        }

        .btn-outline:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
            background: rgba(45, 90, 39, 0.05);
        }

        /* ==================== Tabla Desktop ==================== */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            display: table;
            table-layout: auto;
        }

        .admin-table thead {
            display: table-header-group;
            background: linear-gradient(to right, #f9fafb, #f3f4f6);
        }

        .admin-table tbody {
            display: table-row-group;
        }

        .admin-table tr {
            display: table-row;
        }

        .admin-table th {
            display: table-cell;
            padding: 1rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #4b5563;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }

        .th-content {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .th-content i {
            color: var(--primary-green);
            font-size: 0.75rem;
        }

        .admin-table td {
            display: table-cell;
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .dividend-table-row {
            display: table-row;
            transition: background 0.15s ease;
        }

        .dividend-table-row:hover {
            background: linear-gradient(to right, rgba(45, 90, 39, 0.03), rgba(45, 90, 39, 0.01));
        }

        .dividend-code {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 0.75rem;
            color: #4b5563;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            white-space: nowrap;
        }

        .dividend-code i {
            color: var(--primary-green);
            font-size: 0.65rem;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .user-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.875rem;
        }

        .user-email {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .project-cell {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .project-name {
            font-weight: 600;
            color: #1f2937;
        }

        .project-code {
            font-size: 0.75rem;
            color: #9ca3af;
            font-family: monospace;
        }

        .period-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4f46e5;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .period-badge i {
            font-size: 0.7rem;
        }

        .amount-value {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--primary-green);
        }

        .date-cell {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .date-cell i {
            color: var(--primary-green-light);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-info { background: #dbeafe; color: #1e40af; }
        .status-success { background: #d1fae5; color: #065f46; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .status-danger { background: #fee2e2; color: #991b1b; }
        .status-secondary { background: #e5e7eb; color: #4b5563; }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .action-btn-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .action-btn-success:hover {
            background: var(--success-color);
            color: white;
            transform: scale(1.1);
        }

        .action-btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .action-btn-danger:hover {
            background: var(--danger-color);
            color: white;
            transform: scale(1.1);
        }

        .paid-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--success-color);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .no-actions {
            color: #d1d5db;
        }

        /* ==================== Cards Mobile ==================== */
        .mobile-cards {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .dividend-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .dividend-card:hover {
            border-color: var(--primary-green-light);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .card-header-mobile {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .dividend-code-mobile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #4b5563;
        }

        .dividend-code-mobile i {
            color: var(--primary-green);
        }

        .card-body-mobile {
            padding: 1rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .user-cell-mobile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #1f2937;
        }

        .user-avatar-small {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.7rem;
        }

        .amount-badge {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: var(--primary-green);
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .project-mobile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4b5563;
        }

        .project-mobile i {
            color: var(--primary-green);
        }

        .period-badge-mobile {
            background: #e0e7ff;
            color: #4f46e5;
            padding: 0.25rem 0.625rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .scheduled-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .scheduled-date i {
            color: var(--primary-green-light);
        }

        .card-actions-mobile {
            display: flex;
            gap: 0.5rem;
            padding: 1rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .btn-mobile {
            flex: 1;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-mobile-success {
            background: var(--success-color);
            color: white;
        }

        .btn-mobile-success:hover {
            background: #059669;
        }

        .btn-mobile-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }

        .btn-mobile-danger:hover {
            background: var(--danger-color);
            color: white;
        }

        .card-paid-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1rem;
            background: #d1fae5;
            color: #065f46;
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* ==================== Empty State ==================== */
        .empty-state-container {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-icon-wrapper i {
            font-size: 3rem;
            color: #9ca3af;
            position: relative;
            z-index: 2;
            animation: pulse 2s ease-in-out infinite;
        }

        .empty-icon-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid #e5e7eb;
            border-radius: 50%;
            animation: ring-pulse 2s ease-in-out infinite;
        }

        .empty-state-container h4 {
            font-size: 1.5rem;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .empty-state-container p {
            color: #6b7280;
            max-width: 400px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        /* ==================== Paginacion ==================== */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        /* ==================== Animaciones ==================== */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes ring-pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.3;
            }
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 currentColor;
            }
            70% {
                box-shadow: 0 0 0 8px transparent;
            }
            100% {
                box-shadow: 0 0 0 0 transparent;
            }
        }

        /* ==================== Responsive ==================== */
        .desktop-view {
            display: block;
        }

        .mobile-view {
            display: none;
        }

        @media (max-width: 1024px) {
            .desktop-view {
                display: none;
            }

            .mobile-view {
                display: block;
            }
        }

        @media (max-width: 1200px) {
            .admin-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .admin-stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .admin-stat-card {
                padding: 1rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
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

            .actions-body {
                grid-template-columns: 1fr;
            }

            .section-body {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .admin-stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ==================== DataTables Estilos ==================== */
        .dt-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 0 0.5rem;
        }

        .dt-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.5rem;
            padding: 1rem 0.5rem 0;
            border-top: 1px solid #e5e7eb;
        }

        .dataTables_length {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #4b5563;
        }

        .dataTables_length select {
            padding: 0.5rem 2rem 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .dataTables_length select:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 2px rgba(45, 90, 39, 0.1);
        }

        .dataTables_filter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #4b5563;
        }

        .dataTables_filter input {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            min-width: 200px;
            transition: all 0.2s ease;
        }

        .dataTables_filter input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 2px rgba(45, 90, 39, 0.1);
        }

        .dataTables_info {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .dataTables_paginate {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.875rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 0.875rem;
            color: #4b5563;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
            border-color: var(--primary-green);
            color: var(--primary-green);
            background: rgba(45, 90, 39, 0.05);
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: white;
            font-weight: 600;
        }

        .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Iconos de ordenamiento en headers */
        #dividendos-table thead th {
            cursor: pointer;
            position: relative;
            padding-right: 1.5rem;
        }

        #dividendos-table thead th.sorting::after,
        #dividendos-table thead th.sorting_asc::after,
        #dividendos-table thead th.sorting_desc::after {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 0.65rem;
            color: #9ca3af;
        }

        #dividendos-table thead th.sorting::after {
            content: "\f0dc"; /* sort icon */
        }

        #dividendos-table thead th.sorting_asc::after {
            content: "\f0de"; /* sort-up icon */
            color: var(--primary-green);
        }

        #dividendos-table thead th.sorting_desc::after {
            content: "\f0dd"; /* sort-down icon */
            color: var(--primary-green);
        }

        #dividendos-table thead th:last-child {
            cursor: default;
        }

        #dividendos-table thead th:last-child::after {
            display: none;
        }

        /* Responsive DataTables */
        @media (max-width: 768px) {
            .dt-top, .dt-bottom {
                flex-direction: column;
                align-items: flex-start;
            }

            .dataTables_filter input {
                min-width: 100%;
            }

            .dataTables_paginate {
                flex-wrap: wrap;
            }
        }
    </style>
    @endpush

    @push('scripts')
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        // Inicializar DataTables
        $(document).ready(function() {
            $('#dividendos-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    paginate: {
                        first: "Primero",
                        last: "Ultimo",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "No hay datos disponibles"
                },
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]],
                order: [[5, 'asc']], // Ordenar por columna "Programado" ascendente
                columnDefs: [
                    { orderable: false, targets: [7] } // Columna de acciones no ordenable
                ],
                dom: '<"dt-top"lf>rt<"dt-bottom"ip>',
                drawCallback: function() {
                    // Mantener estilos después de paginar
                    $('.dividend-table-row').css('display', 'table-row');
                }
            });
        });

        function pagarDividendo(id) {
            Swal.fire({
                title: 'Confirmar Pago',
                text: 'Esta seguro de procesar el pago de este dividendo? El monto se acreditara en la billetera del inversionista.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-check"></i> Si, Pagar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal-custom-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        html: 'Por favor espere mientras se procesa el pago',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ url('admin/dividendos') }}/${id}/pagar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Pago Exitoso',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }

        function cancelarDividendo(id) {
            Swal.fire({
                title: 'Cancelar Dividendo',
                html: `
                    <p style="margin-bottom: 1rem; color: #4b5563;">Esta accion no se puede deshacer. El dividendo quedara marcado como cancelado.</p>
                    <input type="text" id="motivo-cancelacion" class="swal2-input" placeholder="Motivo de cancelacion (opcional)" style="margin-top: 0;">
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-times"></i> Cancelar Dividendo',
                cancelButtonText: 'Volver',
                preConfirm: () => {
                    return document.getElementById('motivo-cancelacion').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('admin/dividendos') }}/${id}/cancelar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ motivo: result.value || '' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Dividendo Cancelado',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }

        function procesarTodos() {
            Swal.fire({
                title: 'Procesar Dividendos',
                html: `
                    <div style="text-align: left; padding: 0.5rem 0;">
                        <p style="margin-bottom: 0.75rem; color: #4b5563;">Se procesaran todos los dividendos pendientes cuya fecha programada es hoy o anterior.</p>
                        <div style="background: #f3f4f6; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.9rem; color: #6b7280;">
                            <i class="fas fa-info-circle" style="color: #3b82f6;"></i> Los montos seran acreditados automaticamente en las billeteras de los inversionistas.
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-play-circle"></i> Procesar Todos',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        html: `
                            <div style="padding: 1rem 0;">
                                <p style="color: #6b7280; margin-bottom: 1rem;">Por favor espere mientras se procesan los dividendos</p>
                                <div class="processing-animation" style="display: flex; justify-content: center; gap: 0.5rem;">
                                    <span style="width: 10px; height: 10px; background: #10b981; border-radius: 50%; animation: bounce 0.6s ease-in-out infinite;"></span>
                                    <span style="width: 10px; height: 10px; background: #10b981; border-radius: 50%; animation: bounce 0.6s ease-in-out infinite 0.1s;"></span>
                                    <span style="width: 10px; height: 10px; background: #10b981; border-radius: 50%; animation: bounce 0.6s ease-in-out infinite 0.2s;"></span>
                                </div>
                            </div>
                            <style>
                                @keyframes bounce {
                                    0%, 100% { transform: translateY(0); }
                                    50% { transform: translateY(-10px); }
                                }
                            </style>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    fetch('{{ route("admin.dividends.process-all") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Procesamiento Completado',
                                html: `<div style="text-align: left;">${data.message.replace(/\. /g, '<br>')}</div>`,
                                icon: 'success',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }

        function marcarAtrasados() {
            Swal.fire({
                title: 'Marcar Como Atrasados',
                html: `
                    <div style="text-align: left; padding: 0.5rem 0;">
                        <p style="margin-bottom: 0.75rem; color: #4b5563;">Se marcaran como "atrasados" todos los dividendos programados cuya fecha ya paso y no han sido pagados.</p>
                        <div style="background: #fef3c7; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.9rem; color: #92400e;">
                            <i class="fas fa-exclamation-triangle"></i> Esta accion solo cambia el estado visual. Los dividendos aun pueden ser pagados.
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-exclamation-circle"></i> Marcar Atrasados',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Actualizando...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route("admin.dividends.mark-overdue") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Actualizacion Completada',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al procesar la solicitud',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
