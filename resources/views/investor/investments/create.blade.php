<x-app-layout>
    <x-agromarket.page-header
        title="Invertir en Proyecto"
        subtitle="Ingresa el monto que deseas invertir"
    >
        <x-slot:breadcrumb>
            <a href="{{ route('inversionista.investments.index') }}">Mis Inversiones</a>
            <span>/</span>
            <span>Invertir en {{ $proyecto->nombre }}</span>
        </x-slot:breadcrumb>
    </x-agromarket.page-header>

    <div class="investment-container">
        {{-- Columna izquierda: Información del Proyecto --}}
        <div class="project-summary-card">
            <div class="project-image">
                @if($proyecto->imagenPrincipal())
                    <img src="{{ asset($proyecto->imagenPrincipal()->ruta) }}" alt="{{ $proyecto->nombre }}">
                @else
                    <div class="no-image">
                        <i class="fas fa-seedling"></i>
                    </div>
                @endif
                <div class="project-category">
                    <x-agromarket.badge
                        :color="'primary'"
                        :text="$proyecto->categoria->nombre ?? 'Sin categoría'"
                    />
                </div>
            </div>

            <div class="project-details">
                <h2 class="project-title">{{ $proyecto->nombre }}</h2>

                @if($proyecto->descripcion)
                    <p class="project-description">{{ Str::limit($proyecto->descripcion, 150) }}</p>
                @endif

                <div class="project-stats">
                    <div class="stat-item">
                        <span class="stat-label">ROI Anual</span>
                        <span class="stat-value highlight">{{ $proyecto->roi_anual }}%</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Duración</span>
                        <span class="stat-value">{{ $proyecto->duracion_meses }} meses</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Inversión Mínima</span>
                        <span class="stat-value">${{ number_format($proyecto->inversion_minima, 0, ',', '.') }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Inversión Máxima</span>
                        <span class="stat-value">${{ number_format($proyecto->inversion_maxima, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="funding-progress">
                    <div class="progress-header">
                        <span>Meta de Recaudación</span>
                        <span>${{ number_format($proyecto->monto_recaudado, 0, ',', '.') }} / ${{ number_format($proyecto->monto_objetivo, 0, ',', '.') }}</span>
                    </div>
                    @php
                        $porcentaje = $proyecto->monto_objetivo > 0
                            ? min(100, ($proyecto->monto_recaudado / $proyecto->monto_objetivo) * 100)
                            : 0;
                    @endphp
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $porcentaje }}%"></div>
                    </div>
                    <span class="progress-label">{{ number_format($porcentaje, 1) }}% fondeado | Disponible: ${{ number_format($montoRestante, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Columna derecha: Formulario de Inversión --}}
        <div class="investment-form-card">
            <form action="{{ route('inversionista.investments.contract', $proyecto) }}" method="POST" id="investmentForm">
                @csrf

                <div class="form-header">
                    <h3><i class="fas fa-chart-line"></i> Tu Inversión</h3>
                </div>

                {{-- Saldo Disponible --}}
                <div class="wallet-balance">
                    <div class="balance-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="balance-info">
                        <span class="balance-label">Tu Saldo Disponible</span>
                        <span class="balance-value" id="saldoDisponible" data-value="{{ $billetera->saldo_disponible }}">
                            ${{ number_format($billetera->saldo_disponible, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($billetera->saldo_disponible < $proyecto->inversion_minima)
                        <x-agromarket.badge color="danger" text="Saldo insuficiente" />
                    @else
                        <x-agromarket.badge color="success" text="Saldo suficiente" />
                    @endif
                </div>

                {{-- Input de Monto --}}
                <div class="form-group">
                    <label for="monto">Monto a Invertir</label>
                    <div class="input-with-currency">
                        <span class="currency-symbol">$</span>
                        <input type="number"
                               name="monto"
                               id="monto"
                               class="form-control @error('monto') is-invalid @enderror"
                               min="{{ $proyecto->inversion_minima }}"
                               max="{{ min($proyecto->inversion_maxima, $montoRestante, $billetera->saldo_disponible) }}"
                               step="1000"
                               value="{{ old('monto', $proyecto->inversion_minima) }}"
                               placeholder="Ingresa el monto"
                               required>
                    </div>
                    <div class="input-help">
                        <span>Mínimo: ${{ number_format($proyecto->inversion_minima, 0, ',', '.') }}</span>
                        <span>Máximo: ${{ number_format(min($proyecto->inversion_maxima, $montoRestante), 0, ',', '.') }}</span>
                    </div>
                    @error('monto')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Calculadora de Retornos --}}
                <div class="returns-calculator">
                    <h4><i class="fas fa-calculator"></i> Retornos Estimados</h4>

                    <div class="calculation-row">
                        <span class="calc-label">Monto a invertir</span>
                        <span class="calc-value" id="calcMonto">${{ number_format($proyecto->inversion_minima, 0, ',', '.') }}</span>
                    </div>

                    <div class="calculation-row">
                        <span class="calc-label">ROI Anual</span>
                        <span class="calc-value">{{ $proyecto->roi_anual }}%</span>
                    </div>

                    <div class="calculation-row">
                        <span class="calc-label">Duración</span>
                        <span class="calc-value">{{ $proyecto->duracion_meses }} meses</span>
                    </div>

                    <div class="divider"></div>

                    <div class="calculation-row highlight">
                        <span class="calc-label">Retorno Mensual Estimado</span>
                        <span class="calc-value" id="calcRetornoMensual">${{ number_format($estimacionInicial['retorno_mensual'], 0, ',', '.') }}</span>
                    </div>

                    <div class="calculation-row highlight-primary">
                        <span class="calc-label">Retorno Total Estimado</span>
                        <span class="calc-value" id="calcRetornoTotal">${{ number_format($estimacionInicial['retorno_total'], 0, ',', '.') }}</span>
                    </div>

                    <div class="calculation-row total">
                        <span class="calc-label">Valor al Vencimiento</span>
                        <span class="calc-value" id="calcValorFinal">${{ number_format($estimacionInicial['valor_vencimiento'], 0, ',', '.') }}</span>
                    </div>

                    <p class="disclaimer">
                        <i class="fas fa-info-circle"></i>
                        Los retornos son estimados y pueden variar según el desempeño del proyecto.
                    </p>
                </div>

                {{-- Botón Continuar --}}
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg" id="btnContinuar"
                            {{ $billetera->saldo_disponible < $proyecto->inversion_minima ? 'disabled' : '' }}>
                        <i class="fas fa-file-contract"></i> Continuar al Contrato
                    </button>
                    <a href="{{ route('catalog.show', $proyecto->codigo) }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Volver al Proyecto
                    </a>
                </div>

                @if($billetera->saldo_disponible < $proyecto->inversion_minima)
                    <div class="insufficient-balance-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Tu saldo es insuficiente para invertir en este proyecto. Necesitas al menos ${{ number_format($proyecto->inversion_minima, 0, ',', '.') }}.</p>
                        <a href="#" class="btn btn-sm btn-warning" onclick="Swal.fire({icon: 'info', title: 'Próximamente', text: 'La funcionalidad de depósitos estará disponible pronto.'})">
                            <i class="fas fa-plus"></i> Depositar Fondos
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        .investment-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Project Summary Card */
        .project-summary-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .project-image {
            position: relative;
            height: 250px;
        }

        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .project-image .no-image {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #9ca3af;
        }

        .project-category {
            position: absolute;
            top: 1rem;
            left: 1rem;
        }

        .project-details {
            padding: 1.5rem;
        }

        .project-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 0.75rem 0;
        }

        .project-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .project-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-value.highlight {
            color: #2D5A27;
        }

        .funding-progress {
            background: #f0fdf4;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid #bbf7d0;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .progress-bar {
            height: 10px;
            background: #dcfce7;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, #22c55e, #16a34a);
            border-radius: 5px;
            transition: width 0.5s ease;
        }

        .progress-label {
            font-size: 0.8rem;
            color: #16a34a;
            margin-top: 0.5rem;
            display: block;
        }

        /* Investment Form Card */
        .investment-form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
        }

        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-header h3 i {
            color: #2D5A27;
        }

        /* Wallet Balance */
        .wallet-balance {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .balance-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #22c55e;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
        }

        .balance-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .balance-label {
            font-size: 0.8rem;
            color: #166534;
        }

        .balance-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #166534;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .input-with-currency {
            position: relative;
        }

        .currency-symbol {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
            font-weight: 600;
            color: #6b7280;
        }

        .input-with-currency .form-control {
            padding-left: 2rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2D5A27;
            box-shadow: 0 0 0 4px rgba(45, 90, 39, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        .input-help {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .error-message {
            display: block;
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        /* Returns Calculator */
        .returns-calculator {
            background: linear-gradient(to bottom, #f9fafb, #ffffff);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .returns-calculator h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .returns-calculator h4 i {
            color: #2D5A27;
        }

        .calculation-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 0.9rem;
        }

        .calc-label {
            color: #6b7280;
        }

        .calc-value {
            font-weight: 600;
            color: #1f2937;
        }

        .calculation-row.highlight {
            background: #f0fdf4;
            margin: 0 -1rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .calculation-row.highlight .calc-value {
            color: #16a34a;
        }

        .calculation-row.highlight-primary {
            background: #eff6ff;
            margin: 0 -1rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }

        .calculation-row.highlight-primary .calc-value {
            color: #2563eb;
        }

        .calculation-row.total {
            background: linear-gradient(135deg, #2D5A27, #1e3d1a);
            margin: 0.5rem -1rem 0;
            padding: 1rem;
            border-radius: 12px;
            color: white;
        }

        .calculation-row.total .calc-label,
        .calculation-row.total .calc-value {
            color: white;
            font-size: 1rem;
        }

        .calculation-row.total .calc-value {
            font-size: 1.25rem;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 0.75rem 0;
        }

        .disclaimer {
            font-size: 0.8rem;
            color: #6b7280;
            margin: 1rem 0 0 0;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .disclaimer i {
            color: #9ca3af;
            margin-top: 0.15rem;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2D5A27, #1e3d1a);
            color: white;
            box-shadow: 0 4px 15px rgba(45, 90, 39, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(45, 90, 39, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #e5e7eb;
            color: #4b5563;
        }

        .btn-outline:hover {
            border-color: #2D5A27;
            color: #2D5A27;
            background: rgba(45, 90, 39, 0.05);
        }

        /* Insufficient Balance Alert */
        .insufficient-balance-alert {
            margin-top: 1.5rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }

        .insufficient-balance-alert i {
            color: #dc2626;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .insufficient-balance-alert p {
            color: #991b1b;
            font-size: 0.9rem;
            margin: 0 0 1rem 0;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .investment-container {
                grid-template-columns: 1fr;
            }

            .project-image {
                height: 200px;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const montoInput = document.getElementById('monto');
            const calcMonto = document.getElementById('calcMonto');
            const calcRetornoMensual = document.getElementById('calcRetornoMensual');
            const calcRetornoTotal = document.getElementById('calcRetornoTotal');
            const calcValorFinal = document.getElementById('calcValorFinal');
            const btnContinuar = document.getElementById('btnContinuar');
            const saldoDisponible = parseFloat(document.getElementById('saldoDisponible').dataset.value);

            const roiAnual = {{ $proyecto->roi_anual }};
            const duracionMeses = {{ $proyecto->duracion_meses }};
            const inversionMinima = {{ $proyecto->inversion_minima }};
            const inversionMaxima = {{ min($proyecto->inversion_maxima, $montoRestante) }};

            function formatNumber(num) {
                return '$' + Math.round(num).toLocaleString('es-CO');
            }

            function updateCalculations() {
                let monto = parseFloat(montoInput.value) || 0;

                // Validaciones
                if (monto < inversionMinima) {
                    monto = inversionMinima;
                }
                if (monto > inversionMaxima) {
                    monto = inversionMaxima;
                }

                const retornoMensual = (monto * (roiAnual / 100)) / 12;
                const retornoTotal = (monto * (roiAnual / 100) * duracionMeses) / 12;
                const valorFinal = monto + retornoTotal;

                calcMonto.textContent = formatNumber(monto);
                calcRetornoMensual.textContent = formatNumber(retornoMensual);
                calcRetornoTotal.textContent = formatNumber(retornoTotal);
                calcValorFinal.textContent = formatNumber(valorFinal);

                // Habilitar/deshabilitar botón según saldo
                if (monto > saldoDisponible) {
                    btnContinuar.disabled = true;
                } else {
                    btnContinuar.disabled = false;
                }
            }

            montoInput.addEventListener('input', updateCalculations);
            montoInput.addEventListener('change', updateCalculations);

            // Inicializar
            updateCalculations();
        });
    </script>
    @endpush
</x-app-layout>
