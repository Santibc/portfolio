<x-app-layout>
    <x-agromarket.page-header
        title="Detalle del Retiro"
        :subtitle="$retiro->codigo_retiro"
    />

    @if(session('success'))
        <x-agromarket.alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-agromarket.alert type="error" :message="session('error')" />
    @endif

    <div class="withdrawal-detail-grid">
        {{-- Columna izquierda: Info principal --}}
        <div class="detail-column">
            {{-- Estado actual --}}
            <div class="status-card status-{{ $retiro->estado }}">
                <div class="status-icon">
                    @switch($retiro->estado)
                        @case('pendiente')
                            <i class="fas fa-clock"></i>
                            @break
                        @case('en_revision')
                            <i class="fas fa-search"></i>
                            @break
                        @case('aprobado')
                            <i class="fas fa-check-circle"></i>
                            @break
                        @case('pagado')
                            <i class="fas fa-money-bill-wave"></i>
                            @break
                        @case('rechazado')
                            <i class="fas fa-times-circle"></i>
                            @break
                        @case('cancelado')
                            <i class="fas fa-ban"></i>
                            @break
                    @endswitch
                </div>
                <div class="status-info">
                    <span class="status-label">Estado actual</span>
                    <span class="status-value">{{ ucfirst(str_replace('_', ' ', $retiro->estado)) }}</span>
                </div>
                <div class="status-amount">
                    ${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}
                </div>
            </div>

            {{-- Información del solicitante --}}
            <div class="info-card">
                <div class="card-header">
                    <i class="fas fa-user"></i>
                    <span>Información del Solicitante</span>
                </div>
                <div class="card-body">
                    <div class="user-profile">
                        <div class="user-avatar-large">
                            {{ strtoupper(substr($retiro->usuario->name ?? 'N', 0, 2)) }}
                        </div>
                        <div class="user-info">
                            <h4>{{ $retiro->usuario->name ?? 'N/A' }}</h4>
                            <p><i class="fas fa-envelope"></i> {{ $retiro->usuario->email ?? '' }}</p>
                            @if($retiro->usuario->telefono)
                                <p><i class="fas fa-phone"></i> {{ $retiro->usuario->telefono }}</p>
                            @endif
                        </div>
                    </div>
                    @if($retiro->usuario->billetera)
                        <div class="wallet-summary">
                            <div class="wallet-item">
                                <span class="wallet-label">Saldo disponible</span>
                                <span class="wallet-value">${{ number_format($retiro->usuario->billetera->saldo_disponible, 0, ',', '.') }}</span>
                            </div>
                            <div class="wallet-item">
                                <span class="wallet-label">Saldo bloqueado</span>
                                <span class="wallet-value text-warning">${{ number_format($retiro->usuario->billetera->saldo_bloqueado, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Datos de pago --}}
            @php
                $datosPago = json_decode($retiro->datos_pago, true) ?? [];
            @endphp
            <div class="info-card">
                <div class="card-header">
                    <i class="fas fa-credit-card"></i>
                    <span>Datos de Pago</span>
                </div>
                <div class="card-body">
                    <div class="payment-info-grid">
                        <div class="payment-info-item">
                            <span class="label">Método de pago</span>
                            <span class="value">{{ ucfirst(str_replace('_', ' ', $retiro->metodo_pago)) }}</span>
                        </div>
                        @if(isset($datosPago['banco']))
                            <div class="payment-info-item">
                                <span class="label">Banco</span>
                                <span class="value">{{ $datosPago['banco'] }}</span>
                            </div>
                        @endif
                        @if(isset($datosPago['tipo_cuenta']))
                            <div class="payment-info-item">
                                <span class="label">Tipo de cuenta</span>
                                <span class="value">{{ ucfirst($datosPago['tipo_cuenta']) }}</span>
                            </div>
                        @endif
                        <div class="payment-info-item highlight">
                            <span class="label">{{ $retiro->metodo_pago === 'transferencia_bancaria' ? 'Número de cuenta' : 'Número celular' }}</span>
                            <span class="value">{{ $datosPago['numero_cuenta'] ?? 'N/A' }}</span>
                        </div>
                        <div class="payment-info-item">
                            <span class="label">Titular</span>
                            <span class="value">{{ $datosPago['titular'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Montos --}}
            <div class="info-card">
                <div class="card-header">
                    <i class="fas fa-calculator"></i>
                    <span>Detalle de Montos</span>
                </div>
                <div class="card-body">
                    <div class="amounts-grid">
                        <div class="amount-row">
                            <span>Monto solicitado</span>
                            <span class="amount">${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}</span>
                        </div>
                        @if($retiro->monto_aprobado)
                            <div class="amount-row">
                                <span>Monto aprobado</span>
                                <span class="amount">${{ number_format($retiro->monto_aprobado, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($retiro->comision > 0)
                            <div class="amount-row text-danger">
                                <span>Comisión</span>
                                <span class="amount">-${{ number_format($retiro->comision, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($retiro->monto_neto)
                            <div class="amount-row total">
                                <span>Monto neto a pagar</span>
                                <span class="amount">${{ number_format($retiro->monto_neto, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: Timeline y acciones --}}
        <div class="detail-column">
            {{-- Timeline --}}
            <div class="info-card">
                <div class="card-header">
                    <i class="fas fa-history"></i>
                    <span>Historial del Retiro</span>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($timeline as $event)
                            <div class="timeline-item {{ $event['completado'] ? 'completed' : 'pending' }}">
                                <div class="timeline-marker {{ $event['color'] }}">
                                    <i class="{{ $event['icono'] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h5>{{ $event['titulo'] }}</h5>
                                    @if($event['fecha'])
                                        <span class="timeline-date">
                                            {{ $event['fecha']->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                    @if(isset($event['usuario']))
                                        <span class="timeline-user">
                                            <i class="fas fa-user"></i> {{ $event['usuario'] }}
                                        </span>
                                    @endif
                                    @if(isset($event['motivo']))
                                        <p class="timeline-reason">{{ $event['motivo'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Comprobante de pago --}}
            @if($retiro->comprobante_pago)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-file-invoice"></i>
                        <span>Comprobante de Pago</span>
                    </div>
                    <div class="card-body">
                        <a href="{{ $comprobanteUrl }}" target="_blank" class="proof-link">
                            <i class="fas fa-file-pdf"></i>
                            <span>Ver comprobante</span>
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Notas --}}
            @if($retiro->notas_aprobacion)
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-sticky-note"></i>
                        <span>Notas</span>
                    </div>
                    <div class="card-body">
                        <p class="notes-text">{{ $retiro->notas_aprobacion }}</p>
                    </div>
                </div>
            @endif

            {{-- Motivo de rechazo --}}
            @if($retiro->motivo_rechazo)
                <div class="info-card rejection-card">
                    <div class="card-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Motivo de Rechazo</span>
                    </div>
                    <div class="card-body">
                        <p class="rejection-text">{{ $retiro->motivo_rechazo }}</p>
                    </div>
                </div>
            @endif

            {{-- Acciones --}}
            <div class="actions-card">
                <div class="card-header">
                    <i class="fas fa-cogs"></i>
                    <span>Acciones</span>
                </div>
                <div class="card-body">
                    @if($retiro->estado === 'pendiente' || $retiro->estado === 'en_revision')
                        <button type="button" class="btn btn-success btn-block" onclick="aprobarRetiro()">
                            <i class="fas fa-check"></i> Aprobar Retiro
                        </button>
                        <button type="button" class="btn btn-danger btn-block" onclick="rechazarRetiro()">
                            <i class="fas fa-times"></i> Rechazar Retiro
                        </button>
                    @elseif($retiro->estado === 'aprobado')
                        <button type="button" class="btn btn-success btn-block" onclick="marcarPagado()">
                            <i class="fas fa-money-bill-wave"></i> Marcar como Pagado
                        </button>
                        <button type="button" class="btn btn-danger btn-block" onclick="rechazarRetiro()">
                            <i class="fas fa-times"></i> Rechazar Retiro
                        </button>
                    @else
                        <p class="text-muted text-center">No hay acciones disponibles para este estado.</p>
                    @endif

                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-outline btn-block mt-3">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para rechazo --}}
    <div id="rejectModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-times-circle"></i> Rechazar Retiro</h3>
                <button type="button" class="modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <form action="{{ route('admin.withdrawals.reject', $retiro) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="modal-info">
                        Retiro: <strong>{{ $retiro->codigo_retiro }}</strong><br>
                        Monto: <strong>${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}</strong>
                    </p>
                    <div class="form-group">
                        <label for="motivo_rechazo">Motivo del rechazo *</label>
                        <textarea id="motivo_rechazo" name="motivo_rechazo" class="form-control" rows="4" minlength="10" maxlength="500" required placeholder="Explique el motivo del rechazo..."></textarea>
                        <small class="form-hint">Mínimo 10 caracteres. Este mensaje será visible para el usuario.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeRejectModal()">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal para marcar pagado --}}
    <div id="paymentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-upload"></i> Subir Comprobante de Pago</h3>
                <button type="button" class="modal-close" onclick="closePaymentModal()">&times;</button>
            </div>
            <form action="{{ route('admin.withdrawals.mark-paid', $retiro) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="modal-info">
                        Retiro: <strong>{{ $retiro->codigo_retiro }}</strong><br>
                        Monto a pagar: <strong>${{ number_format($retiro->monto_neto ?? $retiro->monto_solicitado, 0, ',', '.') }}</strong>
                    </p>
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
                    <button type="button" class="btn btn-outline" onclick="closePaymentModal()">Cancelar</button>
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

        .withdrawal-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .detail-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Status Card */
        .status-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 2rem;
            border-radius: 16px;
            color: white;
        }

        .status-card.status-pendiente { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .status-card.status-en_revision { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .status-card.status-aprobado { background: linear-gradient(135deg, #10b981, #059669); }
        .status-card.status-pagado { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .status-card.status-rechazado { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .status-card.status-cancelado { background: linear-gradient(135deg, #6b7280, #4b5563); }

        .status-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .status-info { flex: 1; }
        .status-label { display: block; font-size: 0.9rem; opacity: 0.9; }
        .status-value { font-size: 1.5rem; font-weight: 700; text-transform: capitalize; }
        .status-amount { font-size: 2rem; font-weight: 700; }

        /* Info Cards */
        .info-card, .actions-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(to right, #f9fafb, #ffffff);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            color: #1f2937;
        }

        .card-header i { color: var(--primary-green); }
        .card-body { padding: 1.25rem; }

        /* User Profile */
        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .user-avatar-large {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), #4A7C59);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .user-info h4 { margin: 0 0 0.25rem; color: #1f2937; }
        .user-info p { margin: 0; font-size: 0.9rem; color: #6b7280; }
        .user-info p i { width: 20px; }

        .wallet-summary {
            background: #f9fafb;
            border-radius: 8px;
            padding: 1rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .wallet-item { text-align: center; }
        .wallet-label { display: block; font-size: 0.8rem; color: #6b7280; }
        .wallet-value { font-size: 1.1rem; font-weight: 700; color: var(--primary-green); }
        .wallet-value.text-warning { color: #f59e0b; }

        /* Payment Info */
        .payment-info-grid {
            display: grid;
            gap: 0.75rem;
        }

        .payment-info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .payment-info-item:last-child { border-bottom: none; }
        .payment-info-item .label { color: #6b7280; font-size: 0.9rem; }
        .payment-info-item .value { font-weight: 600; color: #1f2937; }
        .payment-info-item.highlight .value { color: var(--primary-green); font-weight: 700; }

        /* Amounts */
        .amounts-grid { display: flex; flex-direction: column; gap: 0.5rem; }

        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .amount-row:last-child { border-bottom: none; }
        .amount-row .amount { font-weight: 600; }
        .amount-row.total { background: #f0fdf4; margin: 0 -1.25rem; padding: 1rem 1.25rem; border-radius: 8px; }
        .amount-row.total .amount { color: var(--primary-green); font-size: 1.25rem; font-weight: 700; }
        .amount-row.text-danger .amount { color: #ef4444; }

        /* Timeline */
        .timeline { position: relative; padding-left: 2rem; }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .timeline-item:last-child { padding-bottom: 0; }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 2rem;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }

        .timeline-item:last-child::before { display: none; }

        .timeline-marker {
            position: absolute;
            left: -2.5rem;
            top: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.875rem;
        }

        .timeline-marker.primary { background: #3b82f6; }
        .timeline-marker.success { background: #10b981; }
        .timeline-marker.warning { background: #f59e0b; }
        .timeline-marker.danger { background: #ef4444; }
        .timeline-marker.info { background: #06b6d4; }
        .timeline-marker.secondary { background: #6b7280; }

        .timeline-item.pending .timeline-marker { background: #d1d5db; }

        .timeline-content h5 { margin: 0 0 0.25rem; color: #1f2937; font-size: 1rem; }
        .timeline-date { font-size: 0.85rem; color: #6b7280; display: block; }
        .timeline-user { font-size: 0.85rem; color: #6b7280; display: block; }
        .timeline-reason { margin: 0.5rem 0 0; padding: 0.5rem; background: #fef2f2; border-radius: 4px; font-size: 0.9rem; color: #991b1b; }

        /* Proof Link */
        .proof-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .proof-link:hover { background: #dcfce7; }
        .proof-link i:first-child { font-size: 1.5rem; }
        .proof-link i:last-child { margin-left: auto; }

        /* Notes */
        .notes-text { margin: 0; color: #4b5563; line-height: 1.6; }

        /* Rejection Card */
        .rejection-card { border: 2px solid #fecaca; }
        .rejection-card .card-header { background: #fef2f2; }
        .rejection-card .card-header i { color: #ef4444; }
        .rejection-text { margin: 0; color: #991b1b; line-height: 1.6; }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-block { width: 100%; margin-bottom: 0.75rem; }
        .btn-block:last-child { margin-bottom: 0; }

        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }

        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }

        .btn-outline { background: transparent; border: 1px solid #d1d5db; color: #4b5563; }
        .btn-outline:hover { border-color: var(--primary-green); color: var(--primary-green); }

        .mt-3 { margin-top: 1rem; }

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

        .text-muted { color: #6b7280; }
        .text-center { text-align: center; }

        @media (max-width: 992px) {
            .withdrawal-detail-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 576px) {
            .status-card { flex-direction: column; text-align: center; }
            .status-amount { margin-top: 1rem; }
            .user-profile { flex-direction: column; text-align: center; }
            .wallet-summary { grid-template-columns: 1fr; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function aprobarRetiro() {
            Swal.fire({
                title: '¿Aprobar este retiro?',
                html: `
                    <p>Retiro: <strong>{{ $retiro->codigo_retiro }}</strong></p>
                    <p>Monto: <strong>${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}</strong></p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("admin.withdrawals.approve", $retiro) }}';
                    form.innerHTML = '@csrf';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function rechazarRetiro() {
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        function marcarPagado() {
            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        // Cerrar modales al hacer clic fuera
        document.getElementById('rejectModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });

        document.getElementById('paymentModal')?.addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });
    </script>
    @endpush
</x-app-layout>
