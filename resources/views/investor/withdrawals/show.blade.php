<x-app-layout>
    <x-agromarket.page-header
        title="Detalle del Retiro"
        :subtitle="$retiro->codigo_retiro"
    />

    {{-- Alertas --}}
    @if(session('success'))
        <x-agromarket.alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-agromarket.alert type="error" :message="session('error')" />
    @endif

    <div class="withdrawal-detail-container">
        {{-- Estado principal --}}
        <div class="status-banner estado-{{ $retiro->estado }}">
            <div class="status-icon">
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
            <div class="status-info">
                @php
                    $statusLabels = [
                        'pendiente' => 'Pendiente de Aprobación',
                        'en_revision' => 'En Revisión',
                        'aprobado' => 'Aprobado - Pendiente de Pago',
                        'pagado' => 'Pagado',
                        'rechazado' => 'Rechazado',
                        'cancelado' => 'Cancelado',
                    ];
                @endphp
                <span class="status-label">{{ $statusLabels[$retiro->estado] ?? $retiro->estado }}</span>
                <span class="status-amount">${{ number_format($retiro->monto_solicitado, 0, ',', '.') }} COP</span>
            </div>
            @if($retiro->estado === 'pendiente')
                <form action="{{ route('inversionista.withdrawals.cancel', $retiro) }}" method="POST" class="cancel-form">
                    @csrf
                    <button type="button" class="btn btn-cancel" onclick="confirmarCancelacion()">
                        <i class="fas fa-times"></i> Cancelar Retiro
                    </button>
                </form>
            @endif
        </div>

        <div class="detail-grid">
            {{-- Información del retiro --}}
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información del Retiro
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Código</span>
                            <span class="info-value">{{ $retiro->codigo_retiro }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de solicitud</span>
                            <span class="info-value">{{ $retiro->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Monto solicitado</span>
                            <span class="info-value highlight">${{ number_format($retiro->monto_solicitado, 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Método de pago</span>
                            <span class="info-value">{{ ucfirst(str_replace('_', ' ', $retiro->metodo_pago)) }}</span>
                        </div>
                        @if($retiro->monto_aprobado)
                            <div class="info-item">
                                <span class="info-label">Monto aprobado</span>
                                <span class="info-value">${{ number_format($retiro->monto_aprobado, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($retiro->comision > 0)
                            <div class="info-item">
                                <span class="info-label">Comisión</span>
                                <span class="info-value">${{ number_format($retiro->comision, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($retiro->monto_neto)
                            <div class="info-item">
                                <span class="info-label">Monto neto a recibir</span>
                                <span class="info-value highlight">${{ number_format($retiro->monto_neto, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Datos bancarios --}}
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-university"></i> Datos de Pago
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $datosPago = json_decode($retiro->datos_pago, true) ?? [];
                    @endphp
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Titular</span>
                            <span class="info-value">{{ $datosPago['titular'] ?? 'N/A' }}</span>
                        </div>
                        @if(isset($datosPago['banco']))
                            <div class="info-item">
                                <span class="info-label">Banco</span>
                                <span class="info-value">{{ $datosPago['banco'] }}</span>
                            </div>
                        @endif
                        @if(isset($datosPago['tipo_cuenta']))
                            <div class="info-item">
                                <span class="info-label">Tipo de cuenta</span>
                                <span class="info-value">{{ ucfirst($datosPago['tipo_cuenta']) }}</span>
                            </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">{{ $retiro->metodo_pago === 'transferencia_bancaria' ? 'Número de cuenta' : 'Celular' }}</span>
                            <span class="info-value">{{ $datosPago['numero_cuenta'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Motivo de rechazo (si aplica) --}}
        @if($retiro->estado === 'rechazado' && $retiro->motivo_rechazo)
            <div class="alert-card danger">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <h4>Motivo del rechazo</h4>
                    <p>{{ $retiro->motivo_rechazo }}</p>
                </div>
            </div>
        @endif

        {{-- Comprobante de pago (si existe) --}}
        @if($comprobanteUrl)
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice"></i> Comprobante de Pago
                    </h3>
                </div>
                <div class="card-body">
                    <div class="proof-container">
                        <a href="{{ $comprobanteUrl }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-download"></i> Ver / Descargar Comprobante
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Timeline --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history"></i> Historial del Retiro
                </h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($timeline as $item)
                        <div class="timeline-item {{ $item['completado'] ? 'completed' : 'pending' }}">
                            <div class="timeline-marker {{ $item['color'] }}">
                                <i class="{{ $item['icono'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <span class="timeline-title">{{ $item['titulo'] }}</span>
                                @if(isset($item['fecha']) && $item['fecha'])
                                    <span class="timeline-date">
                                        {{ $item['fecha'] instanceof \Carbon\Carbon ? $item['fecha']->format('d/m/Y H:i') : $item['fecha'] }}
                                    </span>
                                @endif
                                @if(isset($item['usuario']))
                                    <span class="timeline-user">Por: {{ $item['usuario'] }}</span>
                                @endif
                                @if(isset($item['motivo']))
                                    <span class="timeline-reason">{{ $item['motivo'] }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Botón volver --}}
        <div class="actions-bar">
            <a href="{{ route('inversionista.withdrawals.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver a mis retiros
            </a>
        </div>
    </div>

    @push('styles')
    <style>
        .withdrawal-detail-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .status-banner {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
        }

        .status-banner.estado-pendiente,
        .status-banner.estado-en_revision {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
        }

        .status-banner.estado-aprobado {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        }

        .status-banner.estado-pagado {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        }

        .status-banner.estado-rechazado {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
        }

        .status-banner.estado-cancelado {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        }

        .status-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: rgba(255,255,255,0.5);
        }

        .estado-pendiente .status-icon,
        .estado-en_revision .status-icon { color: #d97706; }
        .estado-aprobado .status-icon { color: #2563eb; }
        .estado-pagado .status-icon { color: #16a34a; }
        .estado-rechazado .status-icon { color: #dc2626; }
        .estado-cancelado .status-icon { color: #6b7280; }

        .status-info {
            flex: 1;
        }

        .status-label {
            display: block;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .status-amount {
            font-size: 2rem;
            font-weight: 800;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
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

        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed #e5e7eb;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 600;
            color: #1f2937;
        }

        .info-value.highlight {
            color: #2D5A27;
            font-size: 1.1rem;
        }

        .alert-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .alert-card.danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        .alert-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .alert-card.danger .alert-icon {
            background: #dc2626;
            color: white;
        }

        .alert-content h4 {
            margin: 0 0 0.5rem;
            color: #991b1b;
        }

        .alert-content p {
            margin: 0;
            color: #b91c1c;
        }

        .proof-container {
            text-align: center;
            padding: 1rem;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -30px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: white;
        }

        .timeline-marker.primary { background: #2563eb; }
        .timeline-marker.success { background: #16a34a; }
        .timeline-marker.warning { background: #d97706; }
        .timeline-marker.danger { background: #dc2626; }
        .timeline-marker.info { background: #0891b2; }
        .timeline-marker.secondary { background: #6b7280; }

        .timeline-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .timeline-title {
            font-weight: 600;
            color: #1f2937;
        }

        .timeline-date,
        .timeline-user {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .timeline-reason {
            font-size: 0.9rem;
            color: #dc2626;
            font-style: italic;
        }

        .timeline-item.pending .timeline-marker {
            background: #e5e7eb;
        }

        .timeline-item.pending .timeline-title {
            color: #9ca3af;
        }

        .actions-bar {
            margin-top: 1.5rem;
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

        .btn-cancel {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
            border: 1px solid #dc2626;
        }

        .btn-cancel:hover {
            background: #dc2626;
            color: white;
        }

        @media (max-width: 768px) {
            .status-banner {
                flex-direction: column;
                text-align: center;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .info-item {
                flex-direction: column;
                gap: 0.25rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function confirmarCancelacion() {
            Swal.fire({
                title: 'Cancelar Retiro',
                text: '¿Estás seguro de cancelar este retiro? El saldo será devuelto a tu cuenta.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, cancelar retiro',
                cancelButtonText: 'No, mantener'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('.cancel-form').submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
