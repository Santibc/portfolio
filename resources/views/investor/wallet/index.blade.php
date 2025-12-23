<x-app-layout>
    <x-agromarket.page-header
        title="Mi Billetera"
        subtitle="Gestiona tu saldo y revisa tus transacciones"
    />

    {{-- Cards de Saldos --}}
    <div class="stats-grid">
        {{-- Saldo Disponible (destacado) --}}
        <x-agromarket.stat-card
            :title="$summary['saldo_disponible']['titulo']"
            :value="$summary['saldo_disponible']['formateado']"
            :icon="$summary['saldo_disponible']['icono']"
            :color="$summary['saldo_disponible']['color']"
            :description="$summary['saldo_disponible']['descripcion']"
        />

        {{-- Capital Invertido --}}
        <x-agromarket.stat-card
            :title="$summary['saldo_invertido']['titulo']"
            :value="$summary['saldo_invertido']['formateado']"
            :icon="$summary['saldo_invertido']['icono']"
            :color="$summary['saldo_invertido']['color']"
            :description="$summary['saldo_invertido']['descripcion']"
        />

        {{-- Saldo Bloqueado --}}
        <x-agromarket.stat-card
            :title="$summary['saldo_bloqueado']['titulo']"
            :value="$summary['saldo_bloqueado']['formateado']"
            :icon="$summary['saldo_bloqueado']['icono']"
            :color="$summary['saldo_bloqueado']['color']"
            :description="$summary['saldo_bloqueado']['descripcion']"
        />

        {{-- Retornos Acumulados --}}
        <x-agromarket.stat-card
            :title="$summary['retornos_acumulados']['titulo']"
            :value="$summary['retornos_acumulados']['formateado']"
            :icon="$summary['retornos_acumulados']['icono']"
            :color="$summary['retornos_acumulados']['color']"
            :description="$summary['retornos_acumulados']['descripcion']"
        />
    </div>

    {{-- Card de Patrimonio Total --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="total-balance-card">
                <div class="balance-icon">
                    <i class="{{ $summary['saldo_total']['icono'] }}"></i>
                </div>
                <div class="balance-info">
                    <span class="balance-label">{{ $summary['saldo_total']['titulo'] }}</span>
                    <span class="balance-value">{{ $summary['saldo_total']['formateado'] }}</span>
                    <span class="balance-description">{{ $summary['saldo_total']['descripcion'] }}</span>
                </div>
                <div class="balance-actions">
                    <a href="#" class="btn btn-primary" onclick="Swal.fire({icon: 'info', title: 'Próximamente', text: 'La funcionalidad de depósitos estará disponible pronto.'})">
                        <i class="fas fa-plus"></i> Depositar
                    </a>
                    <a href="{{ route('inversionista.withdrawals.create') }}" class="btn btn-outline">
                        <i class="fas fa-minus"></i> Retirar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Próximo Dividendo (si hay) --}}
    @if($summary['dividendos_pendientes']['valor'] > 0)
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dividends-pending-card">
                <div class="pending-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="pending-info">
                    <span class="pending-label">Próximo Dividendo por Recibir</span>
                    <span class="pending-value">{{ $summary['dividendos_pendientes']['formateado'] }}</span>
                    <span class="pending-description">{{ $summary['dividendos_pendientes']['descripcion'] }}</span>
                </div>
                <x-agromarket.badge color="warning" text="Programado" />
            </div>
        </div>
    </div>
    @endif

    {{-- Últimas Transacciones --}}
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Últimas Transacciones
                    </h3>
                    <a href="{{ route('inversionista.wallet.transactions') }}" class="btn btn-sm btn-outline">
                        Ver todas <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="transactions-list">
                            @foreach($recentTransactions as $transaction)
                                <div class="transaction-item">
                                    <div class="transaction-icon {{ $transaction->naturaleza }}">
                                        @if($transaction->naturaleza === 'credito')
                                            <i class="fas fa-arrow-down"></i>
                                        @else
                                            <i class="fas fa-arrow-up"></i>
                                        @endif
                                    </div>
                                    <div class="transaction-info">
                                        <span class="transaction-type">
                                            <x-agromarket.badge
                                                :color="\App\Services\Wallet\WalletService::getTransactionTypeColor($transaction->tipo)"
                                                :text="\App\Services\Wallet\WalletService::getTransactionTypeLabel($transaction->tipo)"
                                            />
                                        </span>
                                        <span class="transaction-description">{{ $transaction->descripcion }}</span>
                                        <span class="transaction-date">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <div class="transaction-amount {{ $transaction->naturaleza }}">
                                        @if($transaction->naturaleza === 'credito')
                                            +${{ number_format($transaction->monto, 0, ',', '.') }}
                                        @else
                                            -${{ number_format($transaction->monto, 0, ',', '.') }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h4>Sin transacciones</h4>
                            <p class="text-muted">Aún no tienes movimientos en tu billetera</p>
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
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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

        /* Total Balance Card - Diseño Premium */
        .total-balance-card {
            background: linear-gradient(135deg, #1e3a5f 0%, #2D5A27 50%, #1e5a3f 100%);
            border-radius: 20px;
            padding: 2.5rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            color: white;
            box-shadow: 0 10px 40px rgba(45, 90, 39, 0.3);
            position: relative;
            overflow: hidden;
        }

        .total-balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .total-balance-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .balance-icon {
            width: 90px;
            height: 90px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
            position: relative;
            z-index: 1;
        }

        .balance-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        .balance-label {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .balance-value {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.1;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .balance-description {
            font-size: 0.9rem;
            opacity: 0.75;
            margin-top: 0.5rem;
        }

        .balance-actions {
            display: flex;
            gap: 1rem;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .balance-actions .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .balance-actions .btn-primary {
            background: white;
            color: #2D5A27;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .balance-actions .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        .balance-actions .btn-outline {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 2px solid rgba(255,255,255,0.4);
            backdrop-filter: blur(5px);
        }

        .balance-actions .btn-outline:hover {
            background: rgba(255,255,255,0.2);
            border-color: white;
            transform: translateY(-3px);
        }

        /* Dividends Pending Card */
        .dividends-pending-card {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: #1f2937;
            box-shadow: 0 4px 20px rgba(251, 191, 36, 0.3);
            animation: pulse 3s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 4px 20px rgba(251, 191, 36, 0.3); }
            50% { box-shadow: 0 4px 30px rgba(251, 191, 36, 0.5); }
        }

        .pending-icon {
            width: 55px;
            height: 55px;
            background: rgba(255,255,255,0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .pending-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .pending-label {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .pending-value {
            font-size: 1.75rem;
            font-weight: 800;
        }

        .pending-description {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-top: 0.25rem;
        }

        /* Transactions List */
        .transactions-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .transaction-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: linear-gradient(to right, #f9fafb, #ffffff);
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .transaction-item:hover {
            background: linear-gradient(to right, #f3f4f6, #f9fafb);
            transform: translateX(5px);
            border-color: #e5e7eb;
        }

        .transaction-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .transaction-icon.credito {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #16a34a;
        }

        .transaction-icon.debito {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
        }

        .transaction-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .transaction-description {
            font-size: 0.95rem;
            color: #374151;
            font-weight: 500;
        }

        .transaction-date {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .transaction-amount {
            font-weight: 700;
            font-size: 1.15rem;
        }

        .transaction-amount.credito {
            color: #16a34a;
        }

        .transaction-amount.debito {
            color: #dc2626;
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
            display: block;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }

        .empty-state p {
            max-width: 300px;
            margin: 0 auto 1.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #4b5563;
            text-decoration: none;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
        }

        .btn-outline:hover {
            border-color: #2D5A27;
            color: #2D5A27;
            background: rgba(45, 90, 39, 0.05);
        }

        .btn-primary {
            background: #2D5A27;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1e3d1a;
            transform: translateY(-2px);
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .text-muted {
            color: #6b7280;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .total-balance-card {
                flex-direction: column;
                text-align: center;
                padding: 2rem;
            }

            .balance-actions {
                width: 100%;
                justify-content: center;
            }

            .balance-value {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .balance-value {
                font-size: 2rem;
            }

            .balance-actions {
                flex-direction: column;
            }

            .balance-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .transaction-item {
                flex-wrap: wrap;
            }

            .transaction-item:hover {
                transform: none;
            }

            .transaction-amount {
                width: 100%;
                text-align: right;
                margin-top: 0.5rem;
                padding-top: 0.5rem;
                border-top: 1px dashed #e5e7eb;
            }
        }
    </style>
    @endpush
</x-app-layout>
