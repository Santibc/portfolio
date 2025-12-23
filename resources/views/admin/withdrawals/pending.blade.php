<x-app-layout>
    <x-agromarket.page-header
        title="Retiros Pendientes"
        subtitle="Solicitudes de retiro pendientes de aprobación"
    />

    @if(session('success'))
        <x-agromarket.alert type="success" :message="session('success')" />
    @endif

    {{-- Resumen --}}
    @php
        $totalPendiente = $retiros->sum('monto_solicitado');
    @endphp
    <div class="pending-summary">
        <div class="summary-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="summary-info">
            <span class="summary-label">Total pendiente de aprobación</span>
            <span class="summary-value">${{ number_format($totalPendiente, 0, ',', '.') }}</span>
        </div>
        <div class="summary-badge">
            <span>{{ $retiros->total() }} solicitudes</span>
        </div>
    </div>

    <div class="admin-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-clock"></i>
                <span>Pendientes de Aprobación</span>
            </div>
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="section-body">
            @if($retiros->count() > 0)
                <div class="withdrawals-list">
                    @foreach($retiros as $retiro)
                        <div class="withdrawal-card">
                            <div class="withdrawal-header">
                                <span class="withdrawal-code">{{ $retiro->codigo_retiro }}</span>
                                <span class="withdrawal-date">{{ $retiro->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="withdrawal-body">
                                <div class="user-info-section">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($retiro->usuario->name ?? 'N', 0, 2)) }}
                                    </div>
                                    <div class="user-details">
                                        <span class="user-name">{{ $retiro->usuario->name ?? 'N/A' }}</span>
                                        <span class="user-email">{{ $retiro->usuario->email ?? '' }}</span>
                                    </div>
                                </div>
                                <div class="withdrawal-amount">
                                    ${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="withdrawal-meta">
                                <span><i class="fas fa-university"></i> {{ ucfirst(str_replace('_', ' ', $retiro->metodo_pago)) }}</span>
                            </div>
                            <div class="withdrawal-actions">
                                <a href="{{ route('admin.withdrawals.show', $retiro) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </a>
                                <button type="button" class="btn btn-success btn-sm" onclick="aprobarRetiro({{ $retiro->id }}, '{{ $retiro->codigo_retiro }}')">
                                    <i class="fas fa-check"></i> Aprobar
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rechazarRetiro({{ $retiro->id }}, '{{ $retiro->codigo_retiro }}')">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrapper mt-4">
                    {{ $retiros->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-check-circle fa-3x"></i>
                    <h4>Sin retiros pendientes</h4>
                    <p>No hay solicitudes de retiro pendientes de aprobación</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Forms ocultos --}}
    <form id="approveForm" method="POST" style="display: none;">
        @csrf
    </form>
    <form id="rejectForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="motivo_rechazo" id="motivoRechazo">
    </form>

    @push('styles')
    <style>
        :root { --primary-green: #2D5A27; }

        /* Pending Summary */
        .pending-summary {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 5px solid #f59e0b;
        }

        .pending-summary .summary-icon {
            width: 60px;
            height: 60px;
            background: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .pending-summary .summary-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .pending-summary .summary-label {
            font-size: 0.9rem;
            color: #92400e;
            font-weight: 500;
        }

        .pending-summary .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: #78350f;
        }

        .pending-summary .summary-badge {
            background: rgba(146, 64, 14, 0.15);
            color: #92400e;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .admin-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
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
        }

        .section-title i { color: var(--primary-green); }
        .section-body { padding: 1.5rem; }

        .withdrawals-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .withdrawal-card {
            border: 2px solid #fef3c7;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, rgba(254, 243, 199, 0.1), white);
            position: relative;
            overflow: hidden;
        }

        .withdrawal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #f59e0b, #fbbf24);
        }

        .withdrawal-card:hover {
            border-color: #f59e0b;
            transform: translateX(4px);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15);
        }

        .withdrawal-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .withdrawal-code {
            font-family: monospace;
            font-weight: 600;
            background: #f3f4f6;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
        }

        .withdrawal-date { color: #6b7280; font-size: 0.9rem; }

        .withdrawal-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .user-info-section { display: flex; align-items: center; gap: 1rem; }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), #4A7C59);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-details { display: flex; flex-direction: column; }
        .user-name { font-weight: 600; color: #1f2937; }
        .user-email { font-size: 0.85rem; color: #6b7280; }

        .withdrawal-amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-green);
        }

        .withdrawal-meta {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
        }

        .withdrawal-meta i { margin-right: 0.25rem; }

        .withdrawal-actions {
            display: flex;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }

        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }

        .btn-outline:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }

        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i { color: #10b981; margin-bottom: 1rem; }
        .empty-state h4 { color: #1f2937; margin-bottom: 0.5rem; }
        .empty-state p { color: #6b7280; }

        .mt-4 { margin-top: 1.5rem; }

        @media (max-width: 768px) {
            .pending-summary {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
            }
            .pending-summary .summary-info {
                align-items: center;
            }
            .withdrawal-body { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .withdrawal-actions { flex-wrap: wrap; }
            .withdrawal-actions .btn { flex: 1; justify-content: center; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function aprobarRetiro(id, codigo) {
            Swal.fire({
                title: 'Aprobar Retiro',
                text: `¿Confirma la aprobación del retiro ${codigo}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('approveForm');
                    form.action = `{{ url('admin/retiros') }}/${id}/aprobar`;
                    form.submit();
                }
            });
        }

        function rechazarRetiro(id, codigo) {
            Swal.fire({
                title: 'Rechazar Retiro',
                html: `
                    <p>¿Está seguro de rechazar el retiro ${codigo}?</p>
                    <p style="font-size: 0.9rem; color: #6b7280;">El saldo será devuelto a la billetera del usuario.</p>
                    <textarea id="swalMotivo" class="swal2-textarea" placeholder="Motivo del rechazo (mínimo 10 caracteres)" style="margin-top: 1rem;"></textarea>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const motivo = document.getElementById('swalMotivo').value;
                    if (motivo.length < 10) {
                        Swal.showValidationMessage('El motivo debe tener al menos 10 caracteres');
                        return false;
                    }
                    return motivo;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('motivoRechazo').value = result.value;
                    const form = document.getElementById('rejectForm');
                    form.action = `{{ url('admin/retiros') }}/${id}/rechazar`;
                    form.submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
