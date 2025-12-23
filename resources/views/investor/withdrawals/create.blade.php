<x-app-layout>
    <x-agromarket.page-header
        title="Solicitar Retiro"
        subtitle="Retira fondos de tu billetera"
    />

    {{-- Alertas --}}
    @if(session('error'))
        <x-agromarket.alert type="error" :message="session('error')" />
    @endif

    {{-- Si tiene retiro pendiente --}}
    @if($tienePendiente)
        <div class="alert-card warning">
            <div class="alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="alert-content">
                <h4>Ya tienes un retiro pendiente</h4>
                <p>Debes esperar a que se procese tu retiro actual antes de solicitar otro.</p>
                <a href="{{ route('inversionista.withdrawals.index') }}" class="btn btn-outline mt-2">
                    <i class="fas fa-eye"></i> Ver mis retiros
                </a>
            </div>
        </div>
    @else
        <div class="withdrawal-container">
            {{-- Info de saldo --}}
            <div class="balance-info-card">
                <div class="balance-section">
                    <span class="balance-label">Saldo Disponible</span>
                    <span class="balance-value">${{ number_format($billetera->saldo_disponible, 0, ',', '.') }}</span>
                </div>
                <div class="balance-divider"></div>
                <div class="limits-section">
                    <div class="limit-item">
                        <span class="limit-label">Mínimo por retiro</span>
                        <span class="limit-value">${{ number_format($montoMinimo, 0, ',', '.') }}</span>
                    </div>
                    <div class="limit-item">
                        <span class="limit-label">Límite diario</span>
                        <span class="limit-value">${{ number_format($limiteDiario, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Formulario de retiro --}}
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave"></i> Datos del Retiro
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('inversionista.withdrawals.store') }}" method="POST" id="withdrawalForm">
                        @csrf

                        {{-- Monto --}}
                        <div class="form-group">
                            <label for="monto">Monto a retirar *</label>
                            <div class="input-with-prefix">
                                <span class="prefix">$</span>
                                <input type="number"
                                       id="monto"
                                       name="monto"
                                       class="form-control @error('monto') is-invalid @enderror"
                                       value="{{ old('monto') }}"
                                       min="{{ $montoMinimo }}"
                                       max="{{ $billetera->saldo_disponible }}"
                                       step="1000"
                                       required>
                            </div>
                            @error('monto')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            <small class="form-hint">Mínimo: ${{ number_format($montoMinimo, 0, ',', '.') }} - Máximo: ${{ number_format($billetera->saldo_disponible, 0, ',', '.') }}</small>
                        </div>

                        {{-- Método de pago --}}
                        <div class="form-group">
                            <label for="metodo_pago">Método de pago *</label>
                            <div class="payment-methods-grid">
                                <label class="payment-method-option {{ old('metodo_pago') == 'transferencia_bancaria' ? 'selected' : '' }}">
                                    <input type="radio" name="metodo_pago" value="transferencia_bancaria"
                                           {{ old('metodo_pago') == 'transferencia_bancaria' ? 'checked' : '' }} required>
                                    <div class="method-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <span class="method-name">Transferencia Bancaria</span>
                                    <span class="method-desc">Bancos nacionales</span>
                                </label>
                                <label class="payment-method-option {{ old('metodo_pago') == 'nequi' ? 'selected' : '' }}">
                                    <input type="radio" name="metodo_pago" value="nequi"
                                           {{ old('metodo_pago') == 'nequi' ? 'checked' : '' }}>
                                    <div class="method-icon nequi">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <span class="method-name">Nequi</span>
                                    <span class="method-desc">Transferencia instantánea</span>
                                </label>
                                <label class="payment-method-option {{ old('metodo_pago') == 'daviplata' ? 'selected' : '' }}">
                                    <input type="radio" name="metodo_pago" value="daviplata"
                                           {{ old('metodo_pago') == 'daviplata' ? 'checked' : '' }}>
                                    <div class="method-icon daviplata">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <span class="method-name">Daviplata</span>
                                    <span class="method-desc">Billetera digital</span>
                                </label>
                            </div>
                            @error('metodo_pago')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campos para transferencia bancaria --}}
                        <div id="campos_banco" style="display: none;">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="banco">Banco *</label>
                                    <select id="banco"
                                            name="banco"
                                            class="form-control @error('banco') is-invalid @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach($bancos as $banco)
                                            <option value="{{ $banco }}" {{ old('banco') == $banco ? 'selected' : '' }}>
                                                {{ $banco }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('banco')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="tipo_cuenta">Tipo de cuenta *</label>
                                    <select id="tipo_cuenta"
                                            name="tipo_cuenta"
                                            class="form-control @error('tipo_cuenta') is-invalid @enderror">
                                        <option value="">Seleccione...</option>
                                        <option value="ahorros" {{ old('tipo_cuenta') == 'ahorros' ? 'selected' : '' }}>
                                            Ahorros
                                        </option>
                                        <option value="corriente" {{ old('tipo_cuenta') == 'corriente' ? 'selected' : '' }}>
                                            Corriente
                                        </option>
                                    </select>
                                    @error('tipo_cuenta')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Número de cuenta / celular --}}
                        <div class="form-group">
                            <label for="numero_cuenta" id="label_numero">Número de cuenta / celular *</label>
                            <input type="text"
                                   id="numero_cuenta"
                                   name="numero_cuenta"
                                   class="form-control @error('numero_cuenta') is-invalid @enderror"
                                   value="{{ old('numero_cuenta') }}"
                                   required>
                            @error('numero_cuenta')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Titular --}}
                        <div class="form-group">
                            <label for="titular">Nombre del titular *</label>
                            <input type="text"
                                   id="titular"
                                   name="titular"
                                   class="form-control @error('titular') is-invalid @enderror"
                                   value="{{ old('titular', auth()->user()->name) }}"
                                   required>
                            @error('titular')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            <small class="form-hint">Debe coincidir con el titular de la cuenta bancaria</small>
                        </div>

                        {{-- Notas --}}
                        <div class="form-group">
                            <label for="notas">Notas adicionales</label>
                            <textarea id="notas"
                                      name="notas"
                                      class="form-control @error('notas') is-invalid @enderror"
                                      rows="3"
                                      maxlength="500">{{ old('notas') }}</textarea>
                            @error('notas')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Resumen --}}
                        <div class="summary-card" id="summaryCard" style="display: none;">
                            <h4><i class="fas fa-calculator"></i> Resumen del retiro</h4>
                            <div class="summary-row">
                                <span>Monto solicitado:</span>
                                <span id="summaryMonto">$0</span>
                            </div>
                            <div class="summary-row">
                                <span>Comisión:</span>
                                <span>$0 (Sin comisión)</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total a recibir:</span>
                                <span id="summaryTotal">$0</span>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="form-actions">
                            <a href="{{ route('inversionista.withdrawals.index') }}" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Solicitar Retiro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('styles')
    <style>
        .withdrawal-container {
            max-width: 700px;
            margin: 0 auto;
        }

        .balance-info-card {
            background: linear-gradient(135deg, #2D5A27 0%, #1e5a3f 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .balance-section {
            flex: 1;
        }

        .balance-label {
            display: block;
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .balance-value {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .balance-divider {
            width: 1px;
            height: 60px;
            background: rgba(255,255,255,0.3);
        }

        .limits-section {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .limit-item {
            display: flex;
            flex-direction: column;
        }

        .limit-label {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .limit-value {
            font-size: 1.1rem;
            font-weight: 600;
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
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #2D5A27;
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        .input-with-prefix {
            display: flex;
            align-items: stretch;
        }

        .input-with-prefix .prefix {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 0.75rem 1rem;
            color: #6b7280;
            font-weight: 600;
        }

        .input-with-prefix .form-control {
            border-radius: 0 8px 8px 0;
        }

        .form-hint {
            display: block;
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        .error-message {
            display: block;
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Payment Methods Grid */
        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .payment-method-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: #fafafa;
        }

        .payment-method-option:hover {
            border-color: #2D5A27;
            background: #f0fdf4;
        }

        .payment-method-option.selected,
        .payment-method-option:has(input:checked) {
            border-color: #2D5A27;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.15);
        }

        .payment-method-option input[type="radio"] {
            display: none;
        }

        .method-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2D5A27, #4A7C59);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            transition: transform 0.3s ease;
        }

        .payment-method-option:hover .method-icon {
            transform: scale(1.1);
        }

        .method-icon.nequi {
            background: linear-gradient(135deg, #E6007E, #FF1493);
        }

        .method-icon.daviplata {
            background: linear-gradient(135deg, #E31837, #FF4500);
        }

        .method-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .method-desc {
            font-size: 0.8rem;
            color: #6b7280;
        }

        /* Campos banco con animación */
        #campos_banco {
            animation: slideDown 0.3s ease-out;
            overflow: hidden;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                max-height: 500px;
                transform: translateY(0);
            }
        }

        .summary-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .summary-card h4 {
            margin: 0 0 1rem;
            color: #374151;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .summary-row.total {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.1rem;
            color: #2D5A27;
            margin-top: 0.5rem;
            padding-top: 1rem;
            border-top: 2px solid #2D5A27;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
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

        .alert-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .alert-card.warning {
            background: #fef3c7;
            border: 1px solid #fbbf24;
        }

        .alert-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .alert-card.warning .alert-icon {
            background: #fbbf24;
            color: white;
        }

        .alert-content h4 {
            margin: 0 0 0.5rem;
            color: #92400e;
        }

        .alert-content p {
            margin: 0;
            color: #a16207;
        }

        .mt-2 { margin-top: 0.5rem; }

        @media (max-width: 768px) {
            .balance-info-card {
                flex-direction: column;
                text-align: center;
            }

            .balance-divider {
                width: 100%;
                height: 1px;
            }

            .limits-section {
                flex-direction: row;
                gap: 2rem;
            }

            .payment-methods-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                flex-direction: column;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptions = document.querySelectorAll('input[name="metodo_pago"]');
            const camposBanco = document.getElementById('campos_banco');
            const labelNumero = document.getElementById('label_numero');
            const montoInput = document.getElementById('monto');
            const summaryCard = document.getElementById('summaryCard');
            const summaryMonto = document.getElementById('summaryMonto');
            const summaryTotal = document.getElementById('summaryTotal');
            const form = document.getElementById('withdrawalForm');

            // Función para actualizar campos según método de pago
            function updatePaymentFields(value) {
                if (value === 'transferencia_bancaria') {
                    camposBanco.style.display = 'block';
                    labelNumero.textContent = 'Número de cuenta *';
                    document.getElementById('banco').required = true;
                    document.getElementById('tipo_cuenta').required = true;
                } else {
                    camposBanco.style.display = 'none';
                    document.getElementById('banco').required = false;
                    document.getElementById('tipo_cuenta').required = false;

                    if (value === 'nequi' || value === 'daviplata') {
                        labelNumero.textContent = 'Número de celular *';
                    } else {
                        labelNumero.textContent = 'Número de cuenta / celular *';
                    }
                }
            }

            // Event listener para radio buttons
            paymentOptions.forEach(option => {
                option.addEventListener('change', function() {
                    // Actualizar clase selected
                    document.querySelectorAll('.payment-method-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    this.closest('.payment-method-option').classList.add('selected');

                    updatePaymentFields(this.value);
                });
            });

            // Actualizar resumen
            montoInput.addEventListener('input', function() {
                const monto = parseFloat(this.value) || 0;
                if (monto > 0) {
                    summaryCard.style.display = 'block';
                    summaryMonto.textContent = '$' + monto.toLocaleString('es-CO');
                    summaryTotal.textContent = '$' + monto.toLocaleString('es-CO');
                } else {
                    summaryCard.style.display = 'none';
                }
            });

            // Confirmar antes de enviar
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const monto = parseFloat(montoInput.value) || 0;

                Swal.fire({
                    title: 'Confirmar Retiro',
                    html: `
                        <p>Estás solicitando un retiro de:</p>
                        <p style="font-size: 1.5rem; font-weight: bold; color: #2D5A27;">
                            $${monto.toLocaleString('es-CO')} COP
                        </p>
                        <p class="text-muted">Este monto será bloqueado hasta que el administrador procese tu solicitud.</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2D5A27',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, solicitar retiro',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Trigger inicial si hay valor seleccionado
            const checkedOption = document.querySelector('input[name="metodo_pago"]:checked');
            if (checkedOption) {
                updatePaymentFields(checkedOption.value);
            }
            if (montoInput.value) {
                montoInput.dispatchEvent(new Event('input'));
            }
        });
    </script>
    @endpush
</x-app-layout>
