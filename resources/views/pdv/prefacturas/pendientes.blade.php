<x-app-layout>
    @section('title', 'Prefacturas Pendientes')

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-receipt me-2"></i>Prefacturas Pendientes
                <span class="badge bg-warning text-dark ms-2" id="totalCount">{{ $prefacturas->count() }}</span>
            </h4>
            <a href="{{ route('pdv.ventas.crear') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver a Venta
            </a>
        </div>

        <div class="row g-3" id="prefacturasGrid">
            @forelse($prefacturas as $pf)
                @php
                    $itemsSinHomologar = $pf->items->filter(function ($it) {
                        $codigo = $it->variante
                            ? ($it->variante->siigo_product_code ?? $it->producto->siigo_product_code ?? null)
                            : ($it->producto->siigo_product_code ?? null);
                        return empty($codigo);
                    });
                    $tieneSinHomologar = $itemsSinHomologar->isNotEmpty();
                    $cantidadSinHomologar = $itemsSinHomologar->count();
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--miracle-lilac) !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold mb-0">{{ $pf->numero_prefactura }}</h6>
                                <small class="text-muted">{{ $pf->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1"><i class="bi bi-person me-1"></i>{{ $pf->nombre_cliente_display }}</p>
                            <p class="mb-1"><i class="bi bi-person-badge me-1"></i>Creó: {{ $pf->usuarioCreador->name ?? '-' }}</p>
                            @if($pf->vendedora_prefactura)
                                <p class="mb-1"><i class="bi bi-person-heart me-1"></i>Vendedora: <strong>{{ $pf->vendedora_prefactura }}</strong></p>
                            @endif
                            <p class="mb-2"><i class="bi bi-box me-1"></i>{{ $pf->items->count() }} producto(s)</p>
                            @if($tieneSinHomologar)
                                <div class="alert alert-warning py-1 px-2 mb-2 small">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    {{ $cantidadSinHomologar }} producto(s) sin homologar con SIIGO
                                </div>
                            @endif
                            <div class="fs-4 fw-bold mb-3" style="color: var(--miracle-pink);">
                                ${{ number_format($pf->total, 2) }}
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pdv.ventas.crear', ['prefactura_id' => $pf->id]) }}" class="btn btn-sm btn-success flex-fill">
                                    <i class="bi bi-pencil-square me-1"></i>Editar y Procesar
                                </a>
                                <button class="btn btn-sm btn-outline-success" onclick="aceptarPrefactura({{ $pf->id }}, {{ $tieneSinHomologar ? 'true' : 'false' }}, {{ $cantidadSinHomologar }})" title="Aceptar sin editar">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="verDetalle({{ $pf->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="anularPrefactura({{ $pf->id }})">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                            No hay prefacturas pendientes
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-lg"><div class="modal-content border-0 shadow" id="modalDetalleContent"></div></div>
    </div>

    @push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function verDetalle(id) {
            fetch(`/pdv/prefacturas/${id}/detalle`).then(r => r.text()).then(html => {
                document.getElementById('modalDetalleContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalDetalle')).show();
            });
        }

        function aceptarPrefactura(id, tieneSinHomologar = false, cantidadSinHomologar = 0) {
            if (tieneSinHomologar) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Productos sin homologar con SIIGO',
                    html: `Esta prefactura tiene <strong>${cantidadSinHomologar}</strong> producto(s) sin homologar con SIIGO y no se puede facturar electrónicamente.<br><br>` +
                          `Debe darle al botón <strong><i class="bi bi-pencil-square"></i> Editar y Procesar</strong> y homologar los productos pendientes antes de generar la factura.`,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#ffc107',
                });
                return;
            }
            Swal.fire({
                title: 'Aceptar Prefactura',
                html: `
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">Método de pago</label>
                        <select id="swal-metodo" class="form-select" onchange="toggleTransferenciaFields()">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="mixto">Mixto</option>
                        </select>
                    </div>
                    <div id="swal-transfer-fields" class="d-none text-start">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipo transferencia</label>
                            <select id="swal-tipo-transfer" class="form-select">
                                <option value="nequi">Nequi</option>
                                <option value="daviplata">Daviplata</option>
                                <option value="transferencia_bancaria">Transferencia Bancaria</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Comprobante (opcional)</label>
                            <input type="file" id="swal-comprobante" class="form-control form-control-sm" accept="image/*,.pdf">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                didOpen: () => {
                    window.toggleTransferenciaFields = function() {
                        const metodo = document.getElementById('swal-metodo').value;
                        const fields = document.getElementById('swal-transfer-fields');
                        fields.classList.toggle('d-none', metodo === 'efectivo');
                    };
                },
                preConfirm: () => {
                    const metodo = document.getElementById('swal-metodo').value;
                    const formData = new FormData();
                    formData.append('metodo_pago', metodo);
                    if (metodo !== 'efectivo') {
                        formData.append('tipo_transferencia', document.getElementById('swal-tipo-transfer').value);
                        const file = document.getElementById('swal-comprobante').files[0];
                        if (file) formData.append('archivo_comprobante', file);
                    }
                    return formData;
                },
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    formData.append('_token', csrfToken);

                    fetch(`/pdv/prefacturas/${id}/aceptar`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: formData,
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.exito) {
                            Swal.fire('Venta creada', data.mensaje, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.mensaje, 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
                }
            });
        }

        function anularPrefactura(id) {
            Swal.fire({
                title: 'Anular Prefactura', input: 'textarea', inputLabel: 'Motivo',
                inputValidator: v => !v || v.length < 5 ? 'Mínimo 5 caracteres' : null,
                showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Anular',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/pdv/prefacturas/${id}/anular`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ motivo_anulacion: result.value }),
                    }).then(r => r.json()).then(data => {
                        Swal.fire(data.exito ? 'Anulada' : 'Error', data.mensaje, data.exito ? 'success' : 'error')
                            .then(() => { if (data.exito) location.reload(); });
                    });
                }
            });
        }

        // Auto-refresh every 15 seconds
        setInterval(() => location.reload(), 30000);
    </script>
    @endpush
</x-app-layout>
