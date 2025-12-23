<x-app-layout>
    <x-agromarket.page-header
        title="Verificacion KYC"
        subtitle="Estado de tu verificacion de identidad"
    />

    <div class="dashboard-row">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-body">
                    {{-- Estado del KYC --}}
                    @if($user->kyc_status === 'pendiente')
                        <div class="kyc-status-card status-warning">
                            <div class="status-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="status-content">
                                <h3 class="status-title">Documentos Pendientes</h3>
                                <p class="status-description">Aun no has subido tus documentos KYC. Debes completar este proceso antes de poder invertir.</p>
                                <a href="{{ route('inversionista.kyc.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-upload"></i> Subir Documentos
                                </a>
                            </div>
                        </div>
                    @elseif($user->kyc_status === 'en_revision')
                        <div class="kyc-status-card status-info">
                            <div class="status-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="status-content">
                                <h3 class="status-title">En Revision</h3>
                                <p class="status-description">Tus documentos estan siendo revisados por nuestro equipo. <strong>Ya puedes invertir</strong> mientras esperamos la aprobacion final.</p>
                                @if($documentos->first()?->created_at)
                                    <p class="status-date"><i class="fas fa-calendar"></i> Fecha de envio: {{ $documentos->first()->created_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @elseif($user->kyc_status === 'aprobado')
                        <div class="kyc-status-card status-success">
                            <div class="status-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="status-content">
                                <h3 class="status-title">Verificado</h3>
                                <p class="status-description">Tu identidad ha sido verificada exitosamente. Puedes invertir sin restricciones.</p>
                                @if($user->kyc_aprobado_at)
                                    <p class="status-date"><i class="fas fa-calendar-check"></i> Aprobado el: {{ $user->kyc_aprobado_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    @elseif($user->kyc_status === 'rechazado')
                        <div class="kyc-status-card status-danger">
                            <div class="status-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="status-content">
                                <h3 class="status-title">Documentos Rechazados</h3>
                                <div class="rejection-reason">
                                    <strong>Motivo:</strong> {{ $user->kyc_notas }}
                                </div>
                                <p class="status-warning-text"><i class="fas fa-exclamation-triangle"></i> No puedes realizar inversiones hasta que subas nuevos documentos.</p>
                                <a href="{{ route('inversionista.kyc.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-upload"></i> Subir Nuevos Documentos
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Documentos subidos --}}
    @if($documentos->count() > 0)
    <div class="dashboard-row mt-4">
        <div class="dashboard-col-12">
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-alt"></i> Documentos Subidos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tipo de Documento</th>
                                    <th>Estado</th>
                                    <th>Fecha de Subida</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documentos as $doc)
                                    <tr>
                                        <td>
                                            <div class="doc-type">
                                                <i class="fas fa-file-alt doc-icon"></i>
                                                <strong>{{ ucfirst(str_replace('_', ' ', $doc->tipo_documento)) }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            @if($doc->estado === 'pendiente' || $doc->estado === 'pendiente_revision')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pendiente
                                                </span>
                                            @elseif($doc->estado === 'aprobado')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Aprobado
                                                </span>
                                            @elseif($doc->estado === 'rechazado')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times"></i> Rechazado
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $doc->observaciones ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('styles')
    <style>
        .dashboard-row {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .dashboard-col-12 {
            flex: 1;
            width: 100%;
        }

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

        /* KYC Status Card */
        .kyc-status-card {
            display: flex;
            gap: 1.5rem;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid;
        }

        .kyc-status-card.status-warning {
            background: linear-gradient(135deg, #fff8e1 0%, #fffde7 100%);
            border-color: #ffc107;
        }

        .kyc-status-card.status-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #e8f4fd 100%);
            border-color: #2196f3;
        }

        .kyc-status-card.status-success {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            border-color: #4caf50;
        }

        .kyc-status-card.status-danger {
            background: linear-gradient(135deg, #ffebee 0%, #fce4ec 100%);
            border-color: #f44336;
        }

        .status-icon {
            flex-shrink: 0;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .status-warning .status-icon {
            background: #ffc107;
            color: #fff;
        }

        .status-info .status-icon {
            background: #2196f3;
            color: #fff;
        }

        .status-success .status-icon {
            background: #4caf50;
            color: #fff;
        }

        .status-danger .status-icon {
            background: #f44336;
            color: #fff;
        }

        .status-content {
            flex: 1;
        }

        .status-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
            color: #1f2937;
        }

        .status-description {
            color: #4b5563;
            margin: 0 0 0.75rem 0;
            font-size: 1rem;
        }

        .status-date {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .rejection-reason {
            background: rgba(244, 67, 54, 0.1);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            color: #c62828;
        }

        .status-warning-text {
            color: #c62828;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        /* Table Styles */
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
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .data-table tr:hover {
            background: #f9fafb;
        }

        .doc-type {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .doc-icon {
            color: #4A7C59;
            font-size: 1.25rem;
        }

        /* Badge styles */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .kyc-status-card {
                flex-direction: column;
                text-align: center;
            }

            .status-icon {
                margin: 0 auto;
            }
        }
    </style>
    @endpush
</x-app-layout>
