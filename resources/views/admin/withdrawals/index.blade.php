<x-app-layout>
    <x-agromarket.page-header
        title="Gestión de Retiros"
        subtitle="Panel de administración para gestionar solicitudes de retiro"
    />

    {{-- Alertas --}}
    @if(session('success'))
        <x-agromarket.alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-agromarket.alert type="error" :message="session('error')" />
    @endif

    {{-- Cards de Estadísticas --}}
    <div class="admin-stats-grid">
        <div class="admin-stat-card stat-warning">
            <div class="stat-icon-wrapper">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Pendientes</span>
                <span class="stat-value">{{ $stats['pendientes']['cantidad'] }}</span>
                <span class="stat-hint">${{ number_format($stats['pendientes']['monto'], 0, ',', '.') }}</span>
            </div>
            @if($stats['pendientes']['cantidad'] > 0)
                <div class="stat-indicator">
                    <span class="pulse-indicator warning"></span>
                </div>
            @endif
        </div>

        <div class="admin-stat-card stat-info">
            <div class="stat-icon-wrapper">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Aprobados (Por Pagar)</span>
                <span class="stat-value">{{ $stats['aprobados']['cantidad'] }}</span>
                <span class="stat-hint">${{ number_format($stats['aprobados']['monto'], 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="admin-stat-card stat-success">
            <div class="stat-icon-wrapper">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Pagados Hoy</span>
                <span class="stat-value">{{ $stats['pagados_hoy']['cantidad'] }}</span>
                <span class="stat-hint">${{ number_format($stats['pagados_hoy']['monto'], 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="admin-stat-card stat-primary">
            <div class="stat-icon-wrapper">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Pagados Este Mes</span>
                <span class="stat-value">{{ $stats['pagados_mes']['cantidad'] }}</span>
                <span class="stat-hint">${{ number_format($stats['pagados_mes']['monto'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Acciones Rápidas --}}
    <div class="admin-section actions-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-bolt"></i>
                <span>Acciones Rápidas</span>
            </div>
        </div>
        <div class="section-body actions-body">
            <a href="{{ route('admin.withdrawals.pending') }}" class="action-card action-warning">
                <div class="action-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="action-content">
                    <span class="action-title">Ver Pendientes</span>
                    <span class="action-desc">{{ $stats['pendientes']['cantidad'] }} solicitudes por aprobar</span>
                </div>
                <i class="fas fa-chevron-right action-arrow"></i>
            </a>

            <a href="{{ route('admin.withdrawals.approved') }}" class="action-card action-success">
                <div class="action-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="action-content">
                    <span class="action-title">Por Pagar</span>
                    <span class="action-desc">{{ $stats['aprobados']['cantidad'] }} retiros aprobados</span>
                </div>
                <i class="fas fa-chevron-right action-arrow"></i>
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="admin-section filters-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-filter"></i>
                <span>Filtros</span>
            </div>
        </div>
        <div class="section-body">
            <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                            <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" name="usuario" id="usuario" class="form-control"
                               value="{{ request('usuario') }}" placeholder="Nombre o email">
                    </div>
                    <div class="filter-group">
                        <label for="fecha_desde">Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                               value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="filter-group">
                        <label for="fecha_hasta">Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                               value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de Retiros --}}
    <div class="admin-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-list"></i>
                <span>Todos los Retiros</span>
            </div>
            <span class="results-badge">{{ $retiros->total() }} registros</span>
        </div>
        <div class="section-body">
            @if($retiros->count() > 0)
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Usuario</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($retiros as $retiro)
                                <tr>
                                    <td>
                                        <span class="code-badge">{{ $retiro->codigo_retiro }}</span>
                                    </td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($retiro->usuario->name ?? 'N', 0, 2)) }}
                                            </div>
                                            <div class="user-info">
                                                <span class="user-name">{{ $retiro->usuario->name ?? 'N/A' }}</span>
                                                <span class="user-email">{{ $retiro->usuario->email ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="amount-value">${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}</span>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $retiro->metodo_pago)) }}</td>
                                    <td>{{ $retiro->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $statusConfig = [
                                                'pendiente' => ['class' => 'warning', 'icon' => 'clock'],
                                                'en_revision' => ['class' => 'info', 'icon' => 'search'],
                                                'aprobado' => ['class' => 'primary', 'icon' => 'check'],
                                                'pagado' => ['class' => 'success', 'icon' => 'check-double'],
                                                'rechazado' => ['class' => 'danger', 'icon' => 'times'],
                                                'cancelado' => ['class' => 'secondary', 'icon' => 'ban'],
                                            ];
                                            $config = $statusConfig[$retiro->estado] ?? ['class' => 'secondary', 'icon' => 'question'];
                                        @endphp
                                        <span class="status-badge status-{{ $config['class'] }}">
                                            <i class="fas fa-{{ $config['icon'] }}"></i>
                                            {{ ucfirst($retiro->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.withdrawals.show', $retiro) }}" class="action-btn action-btn-view" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper mt-4">
                    {{ $retiros->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-money-bill-wave fa-3x"></i>
                    <h4>Sin retiros</h4>
                    <p>No se encontraron retiros con los filtros aplicados</p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        :root {
            --primary-green: #2D5A27;
            --primary-green-light: #4A7C59;
        }

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
            border-left: 4px solid transparent;
        }

        .stat-warning { border-left-color: #f59e0b; }
        .stat-info { border-left-color: #3b82f6; }
        .stat-success { border-left-color: #10b981; }
        .stat-primary { border-left-color: var(--primary-green); }

        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stat-warning .stat-icon-wrapper { background: #fef3c7; color: #f59e0b; }
        .stat-info .stat-icon-wrapper { background: #dbeafe; color: #3b82f6; }
        .stat-success .stat-icon-wrapper { background: #d1fae5; color: #10b981; }
        .stat-primary .stat-icon-wrapper { background: #d4edda; color: var(--primary-green); }

        .stat-content { display: flex; flex-direction: column; gap: 0.25rem; }
        .stat-label { font-size: 0.8rem; font-weight: 600; color: #6b7280; text-transform: uppercase; }
        .stat-value { font-size: 1.75rem; font-weight: 700; color: #1f2937; }
        .stat-hint { font-size: 0.85rem; color: #9ca3af; }

        .stat-indicator { position: absolute; top: 1rem; right: 1rem; }
        .pulse-indicator {
            display: block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #f59e0b;
            animation: pulse-ring 1.5s ease-in-out infinite;
        }

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
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .section-title i { color: var(--primary-green); }
        .section-body { padding: 1.5rem; }

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
            text-decoration: none;
        }

        .action-card.action-warning {
            background: rgba(245, 158, 11, 0.08);
            border-color: rgba(245, 158, 11, 0.2);
        }

        .action-card.action-warning:hover {
            background: rgba(245, 158, 11, 0.15);
            border-color: #f59e0b;
            transform: translateX(4px);
        }

        .action-card.action-success {
            background: rgba(16, 185, 129, 0.08);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .action-card.action-success:hover {
            background: rgba(16, 185, 129, 0.15);
            border-color: #10b981;
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
            color: white;
        }

        .action-warning .action-icon { background: #f59e0b; }
        .action-success .action-icon { background: #10b981; }

        .action-content { flex: 1; display: flex; flex-direction: column; gap: 0.25rem; }
        .action-title { font-weight: 600; color: #1f2937; }
        .action-desc { font-size: 0.85rem; color: #6b7280; }
        .action-arrow { color: #9ca3af; }

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
            min-width: 150px;
            flex: 1;
            max-width: 200px;
        }

        .filter-group label { font-size: 0.875rem; font-weight: 500; color: #374151; }

        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }

        .filter-actions { display: flex; gap: 0.75rem; }

        .results-badge {
            background: #d4edda;
            color: var(--primary-green);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

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
        }

        .btn-primary {
            background: var(--primary-green);
            color: white;
        }

        .btn-primary:hover { background: #1e3d1a; }

        .btn-outline {
            background: transparent;
            border: 2px solid #e5e7eb;
            color: #4b5563;
        }

        .btn-outline:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #4b5563;
            font-size: 0.75rem;
            text-transform: uppercase;
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }

        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .admin-table tr:hover { background: rgba(45, 90, 39, 0.02); }

        .code-badge {
            font-family: monospace;
            font-size: 0.8rem;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .user-cell { display: flex; align-items: center; gap: 0.75rem; }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-light));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .user-info { display: flex; flex-direction: column; }
        .user-name { font-weight: 600; color: #1f2937; font-size: 0.875rem; }
        .user-email { font-size: 0.75rem; color: #9ca3af; }

        .amount-value { font-weight: 700; color: var(--primary-green); }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-warning { background: #fef3c7; color: #92400e; }
        .status-info { background: #dbeafe; color: #1e40af; }
        .status-primary { background: #dbeafe; color: #1e40af; }
        .status-success { background: #d1fae5; color: #065f46; }
        .status-danger { background: #fee2e2; color: #991b1b; }
        .status-secondary { background: #e5e7eb; color: #4b5563; }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .action-btn-view {
            background: rgba(45, 90, 39, 0.1);
            color: var(--primary-green);
        }

        .action-btn-view:hover {
            background: var(--primary-green);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i { color: #9ca3af; margin-bottom: 1rem; }
        .empty-state h4 { color: #1f2937; margin-bottom: 0.5rem; }
        .empty-state p { color: #6b7280; }

        .mt-4 { margin-top: 1.5rem; }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            70% { box-shadow: 0 0 0 8px transparent; }
            100% { box-shadow: 0 0 0 0 transparent; }
        }

        @media (max-width: 1200px) {
            .admin-stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .admin-stats-grid { grid-template-columns: 1fr; }
            .filters-grid { flex-direction: column; }
            .filter-group { max-width: 100%; }
            .filter-actions { width: 100%; }
            .filter-actions .btn { flex: 1; justify-content: center; }
        }
    </style>
    @endpush
</x-app-layout>
