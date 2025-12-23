<x-app-layout>
    <x-agromarket.page-header
        title="Retiros Aprobados"
        subtitle="Retiros pendientes de pago"
    />

    @if(session('success'))
        <x-agromarket.alert type="success" :message="session('success')" />
    @endif

    {{-- Resumen --}}
    <div class="summary-card">
        <div class="summary-icon">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <div class="summary-content">
            <span class="summary-label">Total por pagar</span>
            <span class="summary-value">${{ number_format($totalMonto, 0, ',', '.') }}</span>
        </div>
        <div class="summary-count">
            {{ $retiros->total() }} retiros
        </div>
    </div>

    <div class="admin-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-check-circle"></i>
                <span>Pendientes de Pago</span>
            </div>
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="section-body">
            @if($retiros->count() > 0)
                <div class="withdrawals-list">
                    @foreach($retiros as $retiro)
                        @php
                            $datosPago = json_decode($retiro->datos_pago, true) ?? [];
                        @endphp
                        <div class="withdrawal-card">
                            <div class="withdrawal-header">
                                <span class="withdrawal-code">{{ $retiro->codigo_retiro }}</span>
                                <span class="badge-approved">
                                    <i class="fas fa-check"></i> Aprobado {{ $retiro->fecha_aprobacion?->format('d/m/Y') }}
                                </span>
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
                            <div class="payment-details">
                                <div class="payment-row">
                                    <span class="payment-label">Método:</span>
                                    <span class="payment-value">{{ ucfirst(str_replace('_', ' ', $retiro->metodo_pago)) }}</span>
                                </div>
                                @if(isset($datosPago['banco']))
                                <div class="payment-row">
                                    <span class="payment-label">Banco:</span>
                                    <span class="payment-value">{{ $datosPago['banco'] }}</span>
                                </div>
                                @endif
                                @if(isset($datosPago['tipo_cuenta']))
                                <div class="payment-row">
                                    <span class="payment-label">Tipo cuenta:</span>
                                    <span class="payment-value">{{ ucfirst($datosPago['tipo_cuenta']) }}</span>
                                </div>
                                @endif
                                <div class="payment-row">
                                    <span class="payment-label">{{ $retiro->metodo_pago === 'transferencia_bancaria' ? 'Cuenta:' : 'Celular:' }}</span>
                                    <span class="payment-value highlight">{{ $datosPago['numero_cuenta'] ?? 'N/A' }}</span>
                                </div>
                                <div class="payment-row">
                                    <span class="payment-label">Titular:</span>
                                    <span class="payment-value">{{ $datosPago['titular'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="withdrawal-actions">
                                <a href="{{ route('admin.withdrawals.show', $retiro) }}" class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </a>
                                <button type="button" class="btn btn-success" onclick="marcarPagado({{ $retiro->id }}, '{{ $retiro->codigo_retiro }}')">
                                    <i class="fas fa-money-bill-wave"></i> Marcar como Pagado
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
                    <i class="fas fa-check-double fa-3x"></i>
                    <h4>Sin retiros pendientes de pago</h4>
                    <p>Todos los retiros aprobados han sido pagados</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal para subir comprobante --}}
    <div id="paymentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-upload"></i> Subir Comprobante de Pago</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="paymentForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="modal-info">Retiro: <strong id="modalCodigo"></strong></p>

                    <div class="form-group">
                        <label for="comprobante">Comprobante de pago *</label>
                        <input type="file" id="comprobante" name="comprobante" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-hint">PDF, JPG o PNG (máximo 5MB)</small>
                    </div>

                    <div class="form-group">
                        <label for="notas_aprobacion">Notas (opcional)</label>
                        <textarea id="notas_aprobacion" name="notas_aprobacion" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        :root { --primary-green: #2D5A27; }

        .summary-card {
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .summary-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .summary-content { flex: 1; }
        .summary-label { display: block; font-size: 0.9rem; opacity: 0.9; }
        .summary-value { font-size: 2.5rem; font-weight: 700; }
        .summary-count {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
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
            border: 2px solid #d1fae5;
            border-radius: 12px;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.02), white);
        }

        .withdrawal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .withdrawal-code {
            font-family: monospace;
            font-weight: 600;
            background: #f3f4f6;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .withdrawal-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed #e5e7eb;
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
            color: #10b981;
        }

        .payment-details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 0.375rem 0;
        }

        .payment-label { color: #6b7280; font-size: 0.9rem; }
        .payment-value { font-weight: 500; color: #1f2937; }
        .payment-value.highlight { font-weight: 700; color: var(--primary-green); }

        .withdrawal-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
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

        /* Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            overflow: hidden;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }

        .modal-body { padding: 1.5rem; }

        .modal-info {
            background: #f3f4f6;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .form-hint { font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem; display: block; }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i { color: #10b981; margin-bottom: 1rem; }
        .empty-state h4 { color: #1f2937; margin-bottom: 0.5rem; }
        .empty-state p { color: #6b7280; }

        .mt-4 { margin-top: 1.5rem; }

        @media (max-width: 768px) {
            .summary-card { flex-direction: column; text-align: center; }
            .withdrawal-body { flex-direction: column; gap: 1rem; align-items: flex-start; }
            .withdrawal-actions { flex-wrap: wrap; justify-content: stretch; }
            .withdrawal-actions .btn { flex: 1; justify-content: center; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function marcarPagado(id, codigo) {
            document.getElementById('modalCodigo').textContent = codigo;
            document.getElementById('paymentForm').action = `{{ url('admin/retiros') }}/${id}/marcar-pagado`;
            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('paymentForm').reset();
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
    @endpush
</x-app-layout>
