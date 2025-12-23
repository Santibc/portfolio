<x-app-layout>
    <x-agromarket.page-header
        title="Contrato de Inversión"
        subtitle="Lee y acepta los términos para confirmar tu inversión"
    >
        <x-slot:breadcrumb>
            <a href="{{ route('inversionista.investments.index') }}">Mis Inversiones</a>
            <span>/</span>
            <a href="{{ route('inversionista.investments.create', $proyecto) }}">Invertir</a>
            <span>/</span>
            <span>Contrato</span>
        </x-slot:breadcrumb>
    </x-agromarket.page-header>

    <div class="contract-container">
        {{-- Resumen de Inversión --}}
        <div class="investment-summary-card">
            <h3><i class="fas fa-receipt"></i> Resumen de tu Inversión</h3>

            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Proyecto</span>
                    <span class="summary-value">{{ $proyecto->nombre }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Categoría</span>
                    <span class="summary-value">{{ $proyecto->categoria->nombre ?? 'Sin categoría' }}</span>
                </div>
                <div class="summary-item highlight">
                    <span class="summary-label">Monto a Invertir</span>
                    <span class="summary-value">${{ number_format($monto, 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">ROI Anual</span>
                    <span class="summary-value">{{ $proyecto->roi_anual }}%</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Duración</span>
                    <span class="summary-value">{{ $proyecto->duracion_meses }} meses</span>
                </div>
                <div class="summary-item success">
                    <span class="summary-label">Retorno Total Estimado</span>
                    <span class="summary-value">{{ $estimaciones['retorno_total_formateado'] }}</span>
                </div>
            </div>

            <div class="total-value">
                <span class="total-label">Valor Estimado al Vencimiento</span>
                <span class="total-amount">{{ $estimaciones['valor_vencimiento_formateado'] }}</span>
            </div>
        </div>

        {{-- Contenido del Contrato --}}
        <div class="contract-content-card">
            <div class="contract-header">
                <h3><i class="fas fa-file-contract"></i> Contrato</h3>
                <span class="contract-version">Versión {{ $plantilla->version }}</span>
            </div>

            <div class="contract-body" id="contractContent">
                {!! $contenidoContrato !!}
            </div>

            <div class="contract-scroll-indicator" id="scrollIndicator">
                <i class="fas fa-chevron-down"></i>
                <span>Desplázate para leer todo el contrato</span>
            </div>
        </div>

        {{-- Formulario de Firma --}}
        <div class="signature-card">
            <form action="{{ route('inversionista.investments.store', $proyecto) }}" method="POST" id="signatureForm">
                @csrf
                <input type="hidden" name="monto" value="{{ $monto }}">

                <h3><i class="fas fa-signature"></i> Firma Digital</h3>

                {{-- Checkbox de Aceptación --}}
                <div class="acceptance-checkbox">
                    <label class="checkbox-container">
                        <input type="checkbox" name="acepto_terminos" id="acepto_terminos" value="1" required>
                        <span class="checkmark"></span>
                        <span class="checkbox-text">
                            He leído y acepto todos los términos y condiciones del contrato de inversión.
                            Entiendo los riesgos asociados a esta inversión agrícola.
                        </span>
                    </label>
                    @error('acepto_terminos')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Campo de Firma Digital --}}
                <div class="signature-field">
                    <label for="firma_digital">
                        <i class="fas fa-pen"></i> Escribe tu nombre completo para firmar
                    </label>
                    <input type="text"
                           name="firma_digital"
                           id="firma_digital"
                           class="form-control signature-input @error('firma_digital') is-invalid @enderror"
                           placeholder="Ej: Juan Carlos Pérez García"
                           required
                           autocomplete="off">
                    <p class="signature-hint">Tu firma digital tiene validez legal conforme a la Ley 527 de 1999</p>
                    @error('firma_digital')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Información de Seguridad --}}
                <div class="security-info">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Información de registro:</strong>
                        <p>Se registrará tu IP, fecha, hora y agente de usuario como parte del registro de aceptación.</p>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg" id="btnConfirmar" disabled>
                        <i class="fas fa-check-circle"></i> Firmar y Confirmar Inversión
                    </button>
                    <a href="{{ route('inversionista.investments.create', $proyecto) }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Volver a Modificar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        .contract-container {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Investment Summary Card */
        .investment-summary-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 1.5rem;
        }

        .investment-summary-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #166534;
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .summary-item {
            background: white;
            padding: 0.875rem;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .summary-item.highlight {
            background: #2D5A27;
            color: white;
        }

        .summary-item.highlight .summary-label,
        .summary-item.highlight .summary-value {
            color: white;
        }

        .summary-item.success {
            background: #22c55e;
            color: white;
        }

        .summary-item.success .summary-label,
        .summary-item.success .summary-value {
            color: white;
        }

        .summary-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .total-value {
            background: linear-gradient(135deg, #1e3a5f 0%, #2D5A27 100%);
            border-radius: 16px;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .total-label {
            font-size: 1rem;
            font-weight: 500;
        }

        .total-amount {
            font-size: 1.75rem;
            font-weight: 800;
        }

        /* Contract Content Card */
        .contract-content-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .contract-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .contract-version {
            font-size: 0.8rem;
            color: #6b7280;
            background: #e5e7eb;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .contract-body {
            padding: 2rem;
            max-height: 500px;
            overflow-y: auto;
            font-size: 0.95rem;
            line-height: 1.8;
            color: #374151;
        }

        .contract-body h2 {
            font-size: 1.25rem;
            color: #1f2937;
            margin-bottom: 1.5rem;
        }

        .contract-body h4 {
            font-size: 1rem;
            color: #2D5A27;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .contract-body ul {
            padding-left: 1.5rem;
            margin: 1rem 0;
        }

        .contract-body li {
            margin-bottom: 0.5rem;
        }

        .contract-scroll-indicator {
            padding: 0.75rem;
            background: linear-gradient(to top, #f9fafb 0%, transparent 100%);
            text-align: center;
            font-size: 0.85rem;
            color: #6b7280;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            animation: bounce 2s infinite;
        }

        .contract-scroll-indicator.hidden {
            display: none;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(5px); }
        }

        /* Signature Card */
        .signature-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 2rem;
        }

        .signature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .signature-card h3 i {
            color: #2D5A27;
        }

        /* Checkbox */
        .acceptance-checkbox {
            margin-bottom: 1.5rem;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-container input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 24px;
            height: 24px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            margin-top: 2px;
        }

        .checkbox-container input[type="checkbox"]:checked + .checkmark {
            background: #2D5A27;
            border-color: #2D5A27;
        }

        .checkbox-container input[type="checkbox"]:checked + .checkmark::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: white;
            font-size: 0.8rem;
        }

        .checkbox-text {
            font-size: 0.95rem;
            color: #374151;
            line-height: 1.5;
        }

        /* Signature Field */
        .signature-field {
            margin-bottom: 1.5rem;
        }

        .signature-field label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .signature-field label i {
            color: #2D5A27;
            margin-right: 0.5rem;
        }

        .signature-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1.25rem;
            font-family: 'Brush Script MT', cursive, serif;
            text-align: center;
            transition: all 0.2s ease;
        }

        .signature-input:focus {
            outline: none;
            border-color: #2D5A27;
            box-shadow: 0 0 0 4px rgba(45, 90, 39, 0.1);
        }

        .signature-input.is-invalid {
            border-color: #dc2626;
        }

        .signature-hint {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 0.5rem;
            text-align: center;
        }

        .error-message {
            display: block;
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        /* Security Info */
        .security-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .security-info i {
            color: #2563eb;
            font-size: 1.25rem;
            margin-top: 0.15rem;
        }

        .security-info strong {
            display: block;
            font-size: 0.9rem;
            color: #1e40af;
            margin-bottom: 0.25rem;
        }

        .security-info p {
            font-size: 0.85rem;
            color: #3b82f6;
            margin: 0;
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
            transform: none;
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

        /* Responsive */
        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .total-value {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .signature-input {
                font-size: 1.1rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxAcepto = document.getElementById('acepto_terminos');
            const inputFirma = document.getElementById('firma_digital');
            const btnConfirmar = document.getElementById('btnConfirmar');
            const contractBody = document.querySelector('.contract-body');
            const scrollIndicator = document.getElementById('scrollIndicator');
            const form = document.getElementById('signatureForm');

            function validateForm() {
                const isChecked = checkboxAcepto.checked;
                const hasFirma = inputFirma.value.trim().length >= 3;

                btnConfirmar.disabled = !(isChecked && hasFirma);
            }

            checkboxAcepto.addEventListener('change', validateForm);
            inputFirma.addEventListener('input', validateForm);

            // Ocultar indicador de scroll cuando se llega al final
            contractBody.addEventListener('scroll', function() {
                const isAtBottom = contractBody.scrollHeight - contractBody.scrollTop <= contractBody.clientHeight + 50;
                if (isAtBottom) {
                    scrollIndicator.classList.add('hidden');
                }
            });

            // Confirmación antes de enviar
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: '¿Confirmar inversión?',
                    html: `
                        <p>Estás a punto de invertir <strong>${{ number_format($monto, 0, ',', '.') }} COP</strong> en el proyecto <strong>{{ $proyecto->nombre }}</strong>.</p>
                        <p class="text-sm text-gray-500 mt-2">Este monto se descontará de tu billetera inmediatamente.</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2D5A27',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: '<i class="fas fa-check"></i> Sí, confirmar inversión',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading
                        Swal.fire({
                            title: 'Procesando inversión...',
                            html: 'Por favor espera mientras procesamos tu inversión.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
