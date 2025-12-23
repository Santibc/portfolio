<x-app-layout>
    <x-agromarket.page-header
        title="Mis Retiros"
        subtitle="Historial de solicitudes de retiro"
    />

    {{-- Alertas --}}
    @if(session('success'))
        <x-agromarket.alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-agromarket.alert type="error" :message="session('error')" />
    @endif

    {{-- Resumen de estadísticas --}}
    @php
        $userId = auth()->id();
        $totalPagado = \App\Models\Retiro::where('usuario_id', $userId)->where('estado', 'pagado')->sum('monto_solicitado');
        $totalPendiente = \App\Models\Retiro::where('usuario_id', $userId)->whereIn('estado', ['pendiente', 'aprobado'])->sum('monto_solicitado');
        $cantidadRetiros = \App\Models\Retiro::where('usuario_id', $userId)->where('estado', 'pagado')->count();
        $billetera = auth()->user()->billetera;
    @endphp
    <div class="stats-summary">
        <div class="stat-card stat-available">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Disponible</span>
                <span class="stat-value">${{ number_format($billetera?->saldo_disponible ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="stat-card stat-pending">
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">En proceso</span>
                <span class="stat-value">${{ number_format($totalPendiente, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="stat-card stat-paid">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total retirado</span>
                <span class="stat-value">${{ number_format($totalPagado, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="stat-card stat-count">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Retiros exitosos</span>
                <span class="stat-value">{{ $cantidadRetiros }}</span>
            </div>
        </div>
    </div>

    {{-- Lista de Retiros --}}
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history"></i> Historial de Retiros
            </h3>
            <a href="{{ route('inversionista.withdrawals.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-money-bill-wave"></i> Solicitar Retiro
            </a>
        </div>
        <div class="card-body">
            @if($retiros->count() > 0)
                <div class="withdrawals-list">
                    @foreach($retiros as $retiro)
                        <div class="withdrawal-item">
                            <div class="withdrawal-icon estado-{{ $retiro->estado }}">
                                @switch($retiro->estado)
                                    @case('pendiente')
                                    @case('en_revision')
                                        <i class="fas fa-clock"></i>
                                        @break
                                    @case('aprobado')
                                        <i class="fas fa-check"></i>
                                        @break
                                    @case('pagado')
                                        <i class="fas fa-check-double"></i>
                                        @break
                                    @case('rechazado')
                                        <i class="fas fa-times"></i>
                                        @break
                                    @case('cancelado')
                                        <i class="fas fa-ban"></i>
                                        @break
                                @endswitch
                            </div>
                            <div class="withdrawal-info">
                                <div class="withdrawal-code">{{ $retiro->codigo_retiro }}</div>
                                <div class="withdrawal-meta">
                                    <span class="withdrawal-date">
                                        <i class="fas fa-calendar"></i>
                                        {{ $retiro->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="withdrawal-method">
                                        <i class="fas fa-university"></i>
                                        {{ ucfirst(str_replace('_', ' ', $retiro->metodo_pago)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="withdrawal-amount">
                                ${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}
                            </div>
                            <div class="withdrawal-status">
                                @php
                                    $statusColors = [
                                        'pendiente' => 'warning',
                                        'en_revision' => 'info',
                                        'aprobado' => 'primary',
                                        'pagado' => 'success',
                                        'rechazado' => 'danger',
                                        'cancelado' => 'secondary',
                                    ];
                                    $statusLabels = [
                                        'pendiente' => 'Pendiente',
                                        'en_revision' => 'En Revisión',
                                        'aprobado' => 'Aprobado',
                                        'pagado' => 'Pagado',
                                        'rechazado' => 'Rechazado',
                                        'cancelado' => 'Cancelado',
                                    ];
                                @endphp
                                <x-agromarket.badge
                                    :color="$statusColors[$retiro->estado] ?? 'secondary'"
                                    :text="$statusLabels[$retiro->estado] ?? $retiro->estado"
                                />
                            </div>
                            <div class="withdrawal-actions">
                                <a href="{{ route('inversionista.withdrawals.show', $retiro) }}" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="pagination-wrapper mt-4">
                    {{ $retiros->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-money-bill-wave fa-3x"></i>
                    <h4>Sin retiros</h4>
                    <p class="text-muted">Aún no has solicitado ningún retiro</p>
                    <a href="{{ route('inversionista.withdrawals.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Solicitar mi primer retiro
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        /* Stats Summary */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border-left: 4px solid;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .stat-available { border-left-color: #2D5A27; }
        .stat-pending { border-left-color: #f59e0b; }
        .stat-paid { border-left-color: #10b981; }
        .stat-count { border-left-color: #3b82f6; }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-available .stat-icon { background: #d4edda; color: #2D5A27; }
        .stat-pending .stat-icon { background: #fef3c7; color: #f59e0b; }
        .stat-paid .stat-icon { background: #d1fae5; color: #10b981; }
        .stat-count .stat-icon { background: #dbeafe; color: #3b82f6; }

        .stat-card .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-card .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
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
            flex-wrap: wrap;
            gap: 1rem;
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

        .withdrawals-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .withdrawal-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: linear-gradient(to right, #f9fafb, #ffffff);
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .withdrawal-item:hover {
            border-color: #2D5A27;
            transform: translateX(5px);
        }

        .withdrawal-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .withdrawal-icon.estado-pendiente,
        .withdrawal-icon.estado-en_revision {
            background: #fef3c7;
            color: #d97706;
        }

        .withdrawal-icon.estado-aprobado {
            background: #dbeafe;
            color: #2563eb;
        }

        .withdrawal-icon.estado-pagado {
            background: #dcfce7;
            color: #16a34a;
        }

        .withdrawal-icon.estado-rechazado {
            background: #fee2e2;
            color: #dc2626;
        }

        .withdrawal-icon.estado-cancelado {
            background: #f3f4f6;
            color: #6b7280;
        }

        .withdrawal-info {
            flex: 1;
        }

        .withdrawal-code {
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .withdrawal-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .withdrawal-meta i {
            margin-right: 0.25rem;
        }

        .withdrawal-amount {
            font-weight: 700;
            font-size: 1.25rem;
            color: #1f2937;
        }

        .withdrawal-status {
            min-width: 100px;
            text-align: center;
        }

        .withdrawal-actions {
            flex-shrink: 0;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            color: #9ca3af;
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2D5A27;
            color: white;
        }

        .btn-primary:hover {
            background: #1e3d1a;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
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

        .mb-4 { margin-bottom: 1.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .text-muted { color: #6b7280; }

        @media (max-width: 1024px) {
            .stats-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-summary {
                grid-template-columns: 1fr;
            }

            .withdrawal-item {
                flex-wrap: wrap;
            }

            .withdrawal-amount {
                width: 100%;
                text-align: right;
                margin-top: 0.5rem;
            }

            .withdrawal-status,
            .withdrawal-actions {
                margin-top: 0.5rem;
            }
        }
    </style>
    @endpush
</x-app-layout>
