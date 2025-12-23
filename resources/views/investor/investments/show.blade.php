<x-app-layout>
    <x-agromarket.page-header
        title="{{ $inversion->codigo_inversion }}"
        subtitle="Detalle de tu inversión"
    >
        <x-slot:breadcrumb>
            <a href="{{ route('inversionista.investments.index') }}">Mis Inversiones</a>
            <span>/</span>
            <span>{{ $inversion->codigo_inversion }}</span>
        </x-slot:breadcrumb>
    </x-agromarket.page-header>

    <div class="investment-detail-container">
        {{-- Estado de la Inversión --}}
        <div class="status-banner status-{{ $inversion->estado }}">
            <div class="status-icon">
                @switch($inversion->estado)
                    @case('activa')
                        <i class="fas fa-check-circle"></i>
                        @break
                    @case('pendiente_pago')
                        <i class="fas fa-clock"></i>
                        @break
                    @case('en_trading')
                        <i class="fas fa-exchange-alt"></i>
                        @break
                    @case('vencida')
                        <i class="fas fa-calendar-check"></i>
                        @break
                    @case('vendida')
                        <i class="fas fa-handshake"></i>
                        @break
                    @default
                        <i class="fas fa-info-circle"></i>
                @endswitch
            </div>
            <div class="status-info">
                <span class="status-label">Estado de la Inversión</span>
                <span class="status-text">{{ \App\Services\Investment\InvestmentService::getInvestmentStateLabel($inversion->estado) }}</span>
            </div>
            @if($inversion->estado === 'activa')
                <div class="status-badge">
                    <i class="fas fa-seedling"></i> Generando retornos
                </div>
            @endif
        </div>

        <div class="detail-grid">
            {{-- Columna Izquierda: Información del Proyecto --}}
            <div class="project-info-card">
                <div class="project-header">
                    @if($inversion->proyecto->imagenPrincipal())
                        <img src="{{ asset($inversion->proyecto->imagenPrincipal()->ruta) }}" alt="{{ $inversion->proyecto->nombre }}" class="project-image">
                    @else
                        <div class="project-image no-image">
                            <i class="fas fa-seedling"></i>
                        </div>
                    @endif
                    <div class="project-title-info">
                        <x-agromarket.badge
                            :color="'primary'"
                            :text="$inversion->proyecto->categoria->nombre ?? 'Sin categoría'"
                        />
                        <h3>{{ $inversion->proyecto->nombre }}</h3>
                        <p class="project-code">{{ $inversion->proyecto->codigo }}</p>
                    </div>
                </div>

                <div class="project-stats">
                    <div class="stat-row">
                        <span class="stat-label"><i class="fas fa-map-marker-alt"></i> Ubicación</span>
                        <span class="stat-value">{{ $inversion->proyecto->ubicacion ?? 'No especificada' }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><i class="fas fa-chart-line"></i> ROI Anual</span>
                        <span class="stat-value highlight">{{ $inversion->proyecto->roi_anual }}%</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label"><i class="fas fa-clock"></i> Duración Total</span>
                        <span class="stat-value">{{ $inversion->proyecto->duracion_meses }} meses</span>
                    </div>
                </div>

                <a href="{{ route('catalog.show', $inversion->proyecto->codigo) }}" class="btn btn-outline btn-block">
                    <i class="fas fa-external-link-alt"></i> Ver Proyecto Completo
                </a>
            </div>

            {{-- Columna Derecha: Detalles de Inversión --}}
            <div class="investment-details-card">
                <h3><i class="fas fa-receipt"></i> Detalles de la Inversión</h3>

                <div class="detail-rows">
                    <div class="detail-row">
                        <span class="detail-label">Código de Inversión</span>
                        <span class="detail-value code">{{ $inversion->codigo_inversion }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Fecha de Inversión</span>
                        <span class="detail-value">{{ $inversion->fecha_inversion->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Fecha de Vencimiento</span>
                        <span class="detail-value">{{ $inversion->fecha_vencimiento ? $inversion->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>

                <div class="amounts-section">
                    <div class="amount-card">
                        <span class="amount-label">Monto Invertido</span>
                        <span class="amount-value">${{ number_format($inversion->monto_invertido, 0, ',', '.') }}</span>
                    </div>
                    <div class="amount-card highlight">
                        <span class="amount-label">Valor Actual</span>
                        <span class="amount-value">${{ number_format($inversion->valor_actual, 0, ',', '.') }}</span>
                    </div>
                    @if($inversion->ganancia_acumulada > 0)
                    <div class="amount-card success">
                        <span class="amount-label">Ganancia Acumulada</span>
                        <span class="amount-value">+${{ number_format($inversion->ganancia_acumulada, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($inversion->dividendos_acumulados > 0)
                    <div class="amount-card info">
                        <span class="amount-label">Dividendos Recibidos</span>
                        <span class="amount-value">${{ number_format($inversion->dividendos_acumulados, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Estimaciones --}}
                <div class="estimates-section">
                    <h4>Proyección Estimada</h4>
                    <div class="estimate-row">
                        <span>Retorno Mensual</span>
                        <span class="estimate-value">{{ $estimaciones['retorno_mensual_formateado'] }}</span>
                    </div>
                    <div class="estimate-row">
                        <span>Retorno Total al Vencimiento</span>
                        <span class="estimate-value">{{ $estimaciones['retorno_total_formateado'] }}</span>
                    </div>
                    <div class="estimate-row total">
                        <span>Valor Estimado al Vencimiento</span>
                        <span class="estimate-value">{{ $estimaciones['valor_vencimiento_formateado'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial de Dividendos --}}
        @if($inversion->dividendos->count() > 0)
        <div class="dividends-card">
            <h3><i class="fas fa-coins"></i> Historial de Dividendos</h3>

            <div class="dividends-table">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Período</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inversion->dividendos as $dividendo)
                        <tr>
                            <td>{{ $dividendo->fecha_pago ? $dividendo->fecha_pago->format('d/m/Y') : 'Pendiente' }}</td>
                            <td>{{ $dividendo->periodo ?? 'N/A' }}</td>
                            <td class="amount">${{ number_format($dividendo->monto, 0, ',', '.') }}</td>
                            <td>
                                <x-agromarket.badge
                                    :color="$dividendo->estado === 'pagado' ? 'success' : ($dividendo->estado === 'programado' ? 'warning' : 'secondary')"
                                    :text="ucfirst($dividendo->estado)"
                                />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Contrato Firmado --}}
        @if($inversion->aceptacionContrato)
        <div class="contract-card">
            <div class="contract-header">
                <h3><i class="fas fa-file-contract"></i> Contrato Firmado</h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="toggleContract()">
                    <i class="fas fa-eye" id="contractToggleIcon"></i>
                    <span id="contractToggleText">Ver Contrato</span>
                </button>
            </div>

            <div class="contract-meta">
                <span><i class="fas fa-calendar"></i> Firmado: {{ $inversion->aceptacionContrato->fecha_aceptacion->format('d/m/Y H:i') }}</span>
                <span><i class="fas fa-signature"></i> Firma: {{ $inversion->aceptacionContrato->firma_digital }}</span>
                <span><i class="fas fa-globe"></i> IP: {{ $inversion->aceptacionContrato->ip_aceptacion }}</span>
            </div>

            <div class="contract-content-wrapper" id="contractWrapper" style="display: none;">
                <div class="contract-content">
                    {!! $inversion->aceptacionContrato->contenido_contrato !!}
                </div>
            </div>
        </div>
        @endif

        {{-- Acciones --}}
        <div class="actions-card">
            <h3><i class="fas fa-cogs"></i> Acciones</h3>

            <div class="actions-grid">
                @if($inversion->estado === 'activa' && $inversion->proyecto->categoria && $inversion->proyecto->categoria->permite_trading)
                    <button type="button" class="action-btn trading" onclick="Swal.fire({icon: 'info', title: 'Próximamente', text: 'El marketplace de trading estará disponible pronto.', confirmButtonColor: '#3b82f6'})">
                        <i class="fas fa-exchange-alt"></i>
                        <div class="action-text">
                            <span>Poner en Venta</span>
                            <small>Marketplace Trading</small>
                        </div>
                    </button>
                @endif

                @if($inversion->estado === 'activa' && $inversion->proyecto->categoria && $inversion->proyecto->categoria->permite_retiro_anticipado)
                    <button type="button" class="action-btn withdraw" onclick="Swal.fire({icon: 'info', title: 'Próximamente', text: 'La funcionalidad de retiro anticipado estará disponible pronto.', confirmButtonColor: '#f59e0b'})">
                        <i class="fas fa-sign-out-alt"></i>
                        <div class="action-text">
                            <span>Retiro Anticipado</span>
                            <small>Sujeto a penalización</small>
                        </div>
                    </button>
                @endif

                <a href="{{ route('inversionista.investments.index') }}" class="action-btn back">
                    <i class="fas fa-arrow-left"></i>
                    <div class="action-text">
                        <span>Volver al Listado</span>
                        <small>Mis Inversiones</small>
                    </div>
                </a>

                <a href="{{ route('catalog.index') }}" class="action-btn invest">
                    <i class="fas fa-plus-circle"></i>
                    <div class="action-text">
                        <span>Nueva Inversión</span>
                        <small>Explorar Proyectos</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .investment-detail-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Status Banner */
        .status-banner {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem 2rem;
            border-radius: 20px;
            color: white;
        }

        .status-banner.status-activa {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }

        .status-banner.status-pendiente_pago {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .status-banner.status-en_trading {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .status-banner.status-vencida {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        .status-banner.status-vendida {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .status-banner.status-cancelada {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .status-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .status-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .status-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .status-text {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .status-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Detail Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 1.5rem;
        }

        /* Project Info Card */
        .project-info-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 1.5rem;
        }

        .project-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .project-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .project-image.no-image {
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #9ca3af;
        }

        .project-title-info h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0.75rem 0 0.25rem;
        }

        .project-code {
            font-size: 0.85rem;
            color: #6b7280;
            margin: 0;
        }

        .project-stats {
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
            margin-bottom: 1rem;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-label i {
            color: #9ca3af;
            width: 16px;
        }

        .stat-value {
            font-weight: 600;
            color: #1f2937;
        }

        .stat-value.highlight {
            color: #2D5A27;
            font-size: 1.1rem;
        }

        /* Investment Details Card */
        .investment-details-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 1.5rem;
        }

        .investment-details-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .investment-details-card h3 i {
            color: #2D5A27;
        }

        .detail-rows {
            margin-bottom: 1.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-label {
            font-size: 0.9rem;
            color: #6b7280;
        }

        .detail-value {
            font-weight: 600;
            color: #1f2937;
        }

        .detail-value.code {
            font-family: monospace;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        /* Amounts Section */
        .amounts-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .amount-card {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .amount-card.highlight {
            background: #2D5A27;
        }

        .amount-card.highlight .amount-label,
        .amount-card.highlight .amount-value {
            color: white;
        }

        .amount-card.success {
            background: #dcfce7;
        }

        .amount-card.success .amount-value {
            color: #16a34a;
        }

        .amount-card.info {
            background: #dbeafe;
        }

        .amount-card.info .amount-value {
            color: #2563eb;
        }

        .amount-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .amount-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }

        /* Estimates Section */
        .estimates-section {
            background: linear-gradient(to bottom, #f0fdf4, #ffffff);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1rem;
        }

        .estimates-section h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #166534;
            margin: 0 0 1rem 0;
        }

        .estimate-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 0.9rem;
            color: #374151;
        }

        .estimate-value {
            font-weight: 600;
        }

        .estimate-row.total {
            background: #166534;
            margin: 0.5rem -1rem 0;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: white;
        }

        .estimate-row.total .estimate-value {
            font-size: 1.1rem;
        }

        /* Dividends Card */
        .dividends-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 1.5rem;
        }

        .dividends-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dividends-card h3 i {
            color: #f59e0b;
        }

        .dividends-table {
            overflow-x: auto;
        }

        .dividends-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .dividends-table th,
        .dividends-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .dividends-table th {
            background: #f9fafb;
            font-size: 0.85rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
        }

        .dividends-table td.amount {
            font-weight: 600;
            color: #16a34a;
        }

        /* Contract Card */
        .contract-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 1.5rem;
        }

        .contract-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .contract-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .contract-header h3 i {
            color: #2D5A27;
        }

        .contract-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            font-size: 0.85rem;
            color: #6b7280;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .contract-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .contract-meta i {
            color: #9ca3af;
        }

        .contract-content-wrapper {
            margin-top: 1rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 12px;
            max-height: 400px;
            overflow-y: auto;
        }

        .contract-content {
            font-size: 0.9rem;
            line-height: 1.7;
            color: #374151;
        }

        /* Actions Card */
        .actions-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 1.5rem;
        }

        .actions-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .actions-card h3 i {
            color: #2D5A27;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid #e5e7eb;
            background: linear-gradient(to bottom, #ffffff, #f9fafb);
            font-family: inherit;
            text-align: center;
            min-height: 130px;
            position: relative;
            overflow: visible;
            width: 100%;
            box-sizing: border-box;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: transparent;
            transition: background 0.3s ease;
        }

        .action-btn i {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: #6b7280;
            transition: all 0.3s ease;
        }

        .action-btn span {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1f2937;
            margin-bottom: 0.15rem;
            white-space: nowrap;
        }

        .action-btn small {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        .action-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border-color: transparent;
        }

        /* Trading Button */
        .action-btn.trading {
            border-color: #dbeafe;
            background: linear-gradient(to bottom, #ffffff, #eff6ff);
        }

        .action-btn.trading::before {
            background: linear-gradient(to right, #3b82f6, #2563eb);
        }

        .action-btn.trading i {
            color: #3b82f6;
        }

        .action-btn.trading:hover {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
        }

        /* Withdraw Button */
        .action-btn.withdraw {
            border-color: #fef3c7;
            background: linear-gradient(to bottom, #ffffff, #fffbeb);
        }

        .action-btn.withdraw::before {
            background: linear-gradient(to right, #f59e0b, #d97706);
        }

        .action-btn.withdraw i {
            color: #f59e0b;
        }

        .action-btn.withdraw:hover {
            background: linear-gradient(to bottom, #fffbeb, #fef3c7);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.2);
        }

        /* Back Button */
        .action-btn.back {
            border-color: #e5e7eb;
            background: linear-gradient(to bottom, #ffffff, #f3f4f6);
        }

        .action-btn.back::before {
            background: linear-gradient(to right, #6b7280, #4b5563);
        }

        .action-btn.back i {
            color: #6b7280;
        }

        .action-btn.back:hover {
            background: linear-gradient(to bottom, #f3f4f6, #e5e7eb);
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.15);
        }

        /* Invest Button - Primary Action */
        .action-btn.invest {
            border-color: #bbf7d0;
            background: linear-gradient(to bottom, #ffffff, #f0fdf4);
        }

        .action-btn.invest::before {
            background: linear-gradient(to right, #22c55e, #2D5A27);
        }

        .action-btn.invest i {
            color: #2D5A27;
        }

        .action-btn.invest:hover {
            background: linear-gradient(to bottom, #f0fdf4, #dcfce7);
            box-shadow: 0 8px 25px rgba(45, 90, 39, 0.2);
        }

        .action-btn.invest span {
            color: #166534;
        }

        .action-text {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Buttons */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
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

        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #4b5563;
        }

        .btn-outline:hover {
            border-color: #2D5A27;
            color: #2D5A27;
            background: rgba(45, 90, 39, 0.05);
        }

        .btn-block {
            width: 100%;
            justify-content: center;
        }

        .btn-sm {
            padding: 0.5rem 0.875rem;
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .status-banner {
                flex-wrap: wrap;
            }

            .status-badge {
                width: 100%;
                justify-content: center;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .amounts-section {
                grid-template-columns: 1fr;
            }

            .contract-meta {
                flex-direction: column;
                gap: 0.75rem;
            }

            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .action-btn {
                min-height: 110px;
                padding: 1rem 0.75rem;
            }

            .action-btn i {
                font-size: 1.5rem;
            }

            .action-btn span {
                font-size: 0.85rem;
            }

            .action-btn small {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .actions-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .action-btn {
                flex-direction: row;
                min-height: auto;
                padding: 1rem 1.25rem;
                gap: 1rem;
                justify-content: flex-start;
            }

            .action-btn i {
                margin-bottom: 0;
                font-size: 1.5rem;
                flex-shrink: 0;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(0,0,0,0.05);
                border-radius: 10px;
            }

            .action-text {
                align-items: flex-start;
                flex: 1;
            }

            .action-btn span {
                white-space: normal;
                font-size: 0.9rem;
            }

            .action-btn::before {
                width: 4px;
                height: 100%;
                top: 0;
                left: 0;
                right: auto;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function toggleContract() {
            const wrapper = document.getElementById('contractWrapper');
            const icon = document.getElementById('contractToggleIcon');
            const text = document.getElementById('contractToggleText');

            if (wrapper.style.display === 'none') {
                wrapper.style.display = 'block';
                icon.className = 'fas fa-eye-slash';
                text.textContent = 'Ocultar Contrato';
            } else {
                wrapper.style.display = 'none';
                icon.className = 'fas fa-eye';
                text.textContent = 'Ver Contrato';
            }
        }
    </script>
    @endpush
</x-app-layout>
