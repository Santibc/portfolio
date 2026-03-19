<x-app-layout>
    @section('title', 'Cerrar Caja')

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('pdv.dashboard') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold mb-0"><i class="bi bi-lock me-2"></i>Cerrar Caja — {{ $sesion->caja->nombre }}</h4>
                </div>

                {{-- Resumen del turno --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <div class="h4 fw-bold" style="color: var(--miracle-pink);">${{ number_format($resumen['ventas']['total'], 2) }}</div>
                                <small class="text-muted">Total Ventas ({{ $resumen['ventas']['cantidad'] }})</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <div class="h4 fw-bold text-success">${{ number_format($resumen['ventas']['efectivo'], 2) }}</div>
                                <small class="text-muted">Efectivo Recibido</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <div class="h4 fw-bold text-info">${{ number_format($resumen['ventas']['transferencia'], 2) }}</div>
                                <small class="text-muted">Transferencias</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <div class="h4 fw-bold text-danger">${{ number_format($resumen['vales']['total'], 2) }}</div>
                                <small class="text-muted">Vales ({{ $resumen['vales']['cantidad'] }})</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tr><td class="text-muted">Base (apertura):</td><td class="fw-bold text-end">${{ number_format($resumen['monto_apertura'], 2) }}</td></tr>
                                        <tr><td class="text-muted">(+) Ventas efectivo:</td><td class="text-end">${{ number_format($resumen['ventas']['efectivo'], 2) }}</td></tr>
                                        <tr><td class="text-muted">(-) Cambio entregado:</td><td class="text-end">-${{ number_format($resumen['ventas']['cambio_entregado'], 2) }}</td></tr>
                                        <tr><td class="text-muted">(-) Vales:</td><td class="text-end">-${{ number_format($resumen['vales']['total'], 2) }}</td></tr>
                                        <tr class="table-active"><td class="fw-bold">Efectivo esperado:</td><td class="fw-bold text-end fs-5">${{ number_format($resumen['monto_esperado_efectivo'], 2) }}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('pdv.sesiones.cerrar') }}" method="POST" id="formCerrar">
                                    @csrf
                                    <input type="hidden" name="sesion_id" value="{{ $sesion->id }}">

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Efectivo Contado <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="monto_contado" id="montoContado" class="form-control"
                                                   step="0.01" min="0" required autofocus>
                                        </div>
                                    </div>

                                    <div class="mb-3 p-3 rounded" id="diferencia" style="display:none;">
                                        <div class="text-center">
                                            <span class="fw-bold" id="diferenciLabel"></span>
                                            <div class="fs-3 fw-bold" id="diferenciaValor"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Observaciones</label>
                                        <textarea name="observaciones_cierre" class="form-control" rows="3" placeholder="Observaciones del turno (opcional)"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-lg w-100 btn-danger">
                                        <i class="bi bi-lock me-2"></i>Cerrar Caja
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const esperado = {{ $resumen['monto_esperado_efectivo'] }};
        const montoContadoInput = document.getElementById('montoContado');
        const difDiv = document.getElementById('diferencia');
        const difLabel = document.getElementById('diferenciLabel');
        const difValor = document.getElementById('diferenciaValor');

        montoContadoInput.addEventListener('input', function() {
            const contado = parseFloat(this.value) || 0;
            const dif = contado - esperado;

            difDiv.style.display = 'block';

            if (Math.abs(dif) < 0.01) {
                difDiv.style.background = '#d4edda';
                difLabel.textContent = 'Cuadre exacto';
                difLabel.className = 'fw-bold text-success';
                difValor.textContent = '$0.00';
                difValor.className = 'fs-3 fw-bold text-success';
            } else if (dif > 0) {
                difDiv.style.background = '#cce5ff';
                difLabel.textContent = 'Sobrante';
                difLabel.className = 'fw-bold text-primary';
                difValor.textContent = '+$' + dif.toFixed(2);
                difValor.className = 'fs-3 fw-bold text-primary';
            } else {
                difDiv.style.background = '#f8d7da';
                difLabel.textContent = 'Faltante';
                difLabel.className = 'fw-bold text-danger';
                difValor.textContent = '-$' + Math.abs(dif).toFixed(2);
                difValor.className = 'fs-3 fw-bold text-danger';
            }
        });

        document.getElementById('formCerrar').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Cerrar caja?',
                text: 'Una vez cerrada, las ventas del turno quedarán bloqueadas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sí, cerrar caja',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
