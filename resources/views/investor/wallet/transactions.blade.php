<x-app-layout>
    <x-agromarket.page-header
        title="Historial de Transacciones"
        subtitle="Revisa todos los movimientos de tu billetera"
    />

    {{-- Filtros --}}
    <div class="dashboard-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('inversionista.wallet.transactions') }}" class="filters-form">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="tipo">Tipo de Transacción</label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="">Todos los tipos</option>
                            @foreach($tiposTransaccion as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['tipo'] ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="naturaleza">Naturaleza</label>
                        <select name="naturaleza" id="naturaleza" class="form-control">
                            <option value="">Todas</option>
                            <option value="credito" {{ ($filters['naturaleza'] ?? '') === 'credito' ? 'selected' : '' }}>
                                Crédito (Entradas)
                            </option>
                            <option value="debito" {{ ($filters['naturaleza'] ?? '') === 'debito' ? 'selected' : '' }}>
                                Débito (Salidas)
                            </option>
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
                        <a href="{{ route('inversionista.wallet.transactions') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Transacciones --}}
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Transacciones
            </h3>
            <span class="text-muted">{{ $transactions->total() }} registros encontrados</span>
        </div>
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Saldo Posterior</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <code class="transaction-code">{{ $transaction->codigo_transaccion }}</code>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <span class="date-main">{{ $transaction->created_at->format('d/m/Y') }}</span>
                                            <span class="date-time">{{ $transaction->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <x-agromarket.badge
                                            :color="\App\Services\Wallet\WalletService::getTransactionTypeColor($transaction->tipo)"
                                            :text="\App\Services\Wallet\WalletService::getTransactionTypeLabel($transaction->tipo)"
                                        />
                                    </td>
                                    <td>{{ $transaction->descripcion }}</td>
                                    <td>
                                        <span class="amount {{ $transaction->naturaleza }}">
                                            @if($transaction->naturaleza === 'credito')
                                                +${{ number_format($transaction->monto, 0, ',', '.') }}
                                            @else
                                                -${{ number_format($transaction->monto, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        ${{ number_format($transaction->saldo_posterior, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="pagination-wrapper mt-4">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h4>Sin resultados</h4>
                    <p class="text-muted">No se encontraron transacciones con los filtros seleccionados</p>
                    <a href="{{ route('inversionista.wallet.transactions') }}" class="btn btn-outline mt-3">
                        <i class="fas fa-times"></i> Limpiar filtros
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Botón Volver --}}
    <div class="mt-4">
        <a href="{{ route('inversionista.wallet.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Volver a Mi Billetera
        </a>
    </div>

    @push('styles')
    <style>
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Filters */
        .filters-form {
            width: 100%;
        }

        .filters-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 150px;
        }

        .filter-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #2D5A27;
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
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

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.85rem;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .data-table tr:hover {
            background: #f9fafb;
        }

        .transaction-code {
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #374151;
        }

        .date-cell {
            display: flex;
            flex-direction: column;
        }

        .date-main {
            font-weight: 500;
            color: #1f2937;
        }

        .date-time {
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .amount {
            font-weight: 700;
            font-size: 1rem;
        }

        .amount.credito {
            color: #16a34a;
        }

        .amount.debito {
            color: #dc2626;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper nav {
            display: flex;
            gap: 0.5rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state h4 {
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .filters-row {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .filter-actions {
                width: 100%;
                flex-direction: column;
            }

            .filter-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    @endpush
</x-app-layout>
