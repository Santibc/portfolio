{{-- Modal: Facturación Electrónica SIIGO --}}
<div class="modal fade" id="modalFactura" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Step 1: Ask if client needs invoice --}}
            <div id="facturaStep1">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt-cutoff me-2"></i>Factura Electrónica
                        @if(($siigoModoTest ?? false))
                            <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">PRUEBA</span>
                        @endif
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="mb-4 fs-5">¿El cliente requiere factura electrónica?</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-success px-4" onclick="mostrarFormularioFactura()">
                            <i class="bi bi-check-circle me-1"></i>Sí, generar factura
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="facturarConsumidorFinal()">
                            <i class="bi bi-person me-1"></i>Consumidor Final
                        </button>
                        <button type="button" class="btn btn-outline-dark px-3" onclick="omitirFactura()">
                            <i class="bi bi-x me-1"></i>Omitir
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step 2: Fiscal data form --}}
            <div id="facturaStep2" class="d-none">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt-cutoff me-2"></i>Datos Fiscales
                        @if(($siigoModoTest ?? false))
                            <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">PRUEBA</span>
                        @endif
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="volverStep1()">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formDatosFiscales">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipo Documento</label>
                                <select class="form-select" id="facturaTipoDocumento">
                                    <option value="13">Cédula de Ciudadanía</option>
                                    <option value="31">NIT</option>
                                    <option value="22">Cédula de Extranjería</option>
                                    <option value="41">Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Número de Identificación</label>
                                <input type="text" class="form-control" id="facturaNumeroId" required>
                            </div>
                            <div class="col-12" id="facturaRazonSocialGroup" style="display:none;">
                                <label class="form-label fw-semibold">Razón Social</label>
                                <input type="text" class="form-control" id="facturaRazonSocial">
                            </div>
                            <div class="col-12" id="facturaNombreGroup">
                                <label class="form-label fw-semibold">Nombre Completo</label>
                                <input type="text" class="form-control" id="facturaNombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email para factura</label>
                                <input type="email" class="form-control" id="facturaEmail">
                                <small class="text-muted">Se enviará el PDF de la factura</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" class="form-control" id="facturaTelefono">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="generarFacturaConDatos()">
                        <i class="bi bi-send me-1"></i>Generar Factura Electrónica
                    </button>
                </div>
            </div>

            {{-- Step 3: Processing / Result --}}
            <div id="facturaStep3" class="d-none">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt-cutoff me-2"></i>Resultado Facturación
                        @if(($siigoModoTest ?? false))
                            <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">PRUEBA</span>
                        @endif
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    {{-- Loading state --}}
                    <div id="facturaLoading">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <p class="fs-5">Generando factura electrónica...</p>
                        <p class="text-muted">Comunicándose con SIIGO / DIAN</p>
                    </div>

                    {{-- Result state --}}
                    <div id="facturaResultado" class="d-none">
                        <div id="facturaIcono" class="mb-3"></div>
                        <h5 id="facturaEstadoTexto" class="fw-bold mb-2"></h5>
                        <p id="facturaMensaje" class="text-muted"></p>
                        <div id="facturaCufeBox" class="d-none mt-3 p-3 bg-light rounded">
                            <small class="text-muted d-block">CUFE:</small>
                            <code id="facturaCufe" class="text-break"></code>
                        </div>
                        <div id="facturaNumeroBox" class="d-none mt-2">
                            <small class="text-muted">N° Factura: </small>
                            <strong id="facturaNumero"></strong>
                        </div>
                        <div id="facturaErrorBox" class="d-none mt-3 alert alert-danger text-start">
                            <small id="facturaErrorTexto"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" id="btnReintentarFactura" style="display:none;" onclick="reintentarFacturaDesdModal()">
                        <i class="bi bi-arrow-repeat me-1"></i>Reintentar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="cerrarModalFactura()">
                        <i class="bi bi-check me-1"></i>Continuar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let ventaIdParaFactura = null;

    function abrirModalFactura(ventaId, clienteData) {
        ventaIdParaFactura = ventaId;

        // Reset to step 1
        document.getElementById('facturaStep1').classList.remove('d-none');
        document.getElementById('facturaStep2').classList.add('d-none');
        document.getElementById('facturaStep3').classList.add('d-none');

        // Pre-fill from client data if available
        if (clienteData) {
            document.getElementById('facturaNumeroId').value = clienteData.documento || '';
            document.getElementById('facturaNombre').value = clienteData.nombre || '';
            document.getElementById('facturaEmail').value = clienteData.email || '';
            document.getElementById('facturaTelefono').value = clienteData.telefono || '';

            // Auto-detect document type
            if (clienteData.tipo_documento) {
                document.getElementById('facturaTipoDocumento').value = clienteData.tipo_documento;
            }
        }

        const modal = new bootstrap.Modal(document.getElementById('modalFactura'));
        modal.show();
    }

    function mostrarFormularioFactura() {
        document.getElementById('facturaStep1').classList.add('d-none');
        document.getElementById('facturaStep2').classList.remove('d-none');
    }

    function volverStep1() {
        document.getElementById('facturaStep2').classList.add('d-none');
        document.getElementById('facturaStep1').classList.remove('d-none');
    }

    // Toggle razón social field for NIT
    document.getElementById('facturaTipoDocumento').addEventListener('change', function() {
        const esNit = this.value === '31';
        document.getElementById('facturaRazonSocialGroup').style.display = esNit ? '' : 'none';
        document.getElementById('facturaNombreGroup').querySelector('label').textContent =
            esNit ? 'Nombre del Representante Legal' : 'Nombre Completo';
    });

    function facturarConsumidorFinal() {
        mostrarPasoResultado();
        enviarFactura({ tipo_factura: 'consumidor_final' });
    }

    function generarFacturaConDatos() {
        const tipoDoc = document.getElementById('facturaTipoDocumento').value;
        const numId = document.getElementById('facturaNumeroId').value.trim();
        const nombre = document.getElementById('facturaNombre').value.trim();
        const email = document.getElementById('facturaEmail').value.trim();

        if (!numId || !nombre) {
            alert('Por favor ingrese el número de identificación y nombre.');
            return;
        }

        mostrarPasoResultado();
        enviarFactura({
            tipo_factura: 'con_cliente',
            tipo_documento: tipoDoc,
            numero_identificacion: numId,
            nombre_fiscal: nombre,
            razon_social: document.getElementById('facturaRazonSocial')?.value || '',
            email_factura: email,
            telefono: document.getElementById('facturaTelefono').value.trim(),
        });
    }

    function mostrarPasoResultado() {
        document.getElementById('facturaStep1').classList.add('d-none');
        document.getElementById('facturaStep2').classList.add('d-none');
        document.getElementById('facturaStep3').classList.remove('d-none');
        document.getElementById('facturaLoading').classList.remove('d-none');
        document.getElementById('facturaResultado').classList.add('d-none');
    }

    function enviarFactura(data) {
        fetch(`/pdv/ventas/${ventaIdParaFactura}/factura/generar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => mostrarResultadoFactura(data))
        .catch(err => mostrarResultadoFactura({
            exito: false,
            mensaje: 'Error de conexión: ' + err.message,
            factura: { estado_dian: 'error' }
        }));
    }

    function mostrarResultadoFactura(data) {
        document.getElementById('facturaLoading').classList.add('d-none');
        document.getElementById('facturaResultado').classList.remove('d-none');

        const icono = document.getElementById('facturaIcono');
        const estadoTexto = document.getElementById('facturaEstadoTexto');
        const mensaje = document.getElementById('facturaMensaje');
        const cufeBox = document.getElementById('facturaCufeBox');
        const numeroBox = document.getElementById('facturaNumeroBox');
        const errorBox = document.getElementById('facturaErrorBox');
        const btnReintentar = document.getElementById('btnReintentarFactura');

        cufeBox.classList.add('d-none');
        numeroBox.classList.add('d-none');
        errorBox.classList.add('d-none');
        btnReintentar.style.display = 'none';

        const estado = data.factura?.estado_dian || 'error';

        const esPrueba = data.modo_prueba || false;
        const prefijoPrueba = esPrueba ? '(PRUEBA) ' : '';

        if (estado === 'aprobada') {
            icono.innerHTML = '<i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>';
            estadoTexto.textContent = prefijoPrueba + 'Factura Aprobada por DIAN';
            estadoTexto.className = 'fw-bold mb-2 text-success';

            if (data.factura?.cufe) {
                cufeBox.classList.remove('d-none');
                document.getElementById('facturaCufe').textContent = data.factura.cufe;
            }
            if (data.factura?.numero_factura) {
                numeroBox.classList.remove('d-none');
                document.getElementById('facturaNumero').textContent = data.factura.numero_factura;
            }
        } else if (estado === 'pendiente') {
            icono.innerHTML = '<i class="bi bi-clock-fill text-warning" style="font-size: 4rem;"></i>';
            estadoTexto.textContent = prefijoPrueba + 'Factura Pendiente';
            estadoTexto.className = 'fw-bold mb-2 text-warning';
            btnReintentar.style.display = '';
        } else {
            icono.innerHTML = '<i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>';
            estadoTexto.textContent = prefijoPrueba + 'Error en Facturación';
            estadoTexto.className = 'fw-bold mb-2 text-danger';
            btnReintentar.style.display = '';

            if (data.factura?.errores || data.mensaje) {
                errorBox.classList.remove('d-none');
                document.getElementById('facturaErrorTexto').textContent = data.factura?.errores || data.mensaje;
            }
        }

        mensaje.textContent = data.mensaje || '';
    }

    function reintentarFacturaDesdModal() {
        mostrarPasoResultado();
        fetch(`/pdv/ventas/${ventaIdParaFactura}/factura/reintentar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => mostrarResultadoFactura(data))
        .catch(err => mostrarResultadoFactura({
            exito: false,
            mensaje: 'Error: ' + err.message,
            factura: { estado_dian: 'error' }
        }));
    }

    function omitirFactura() {
        bootstrap.Modal.getInstance(document.getElementById('modalFactura')).hide();
        // Show the success modal that was waiting
        if (typeof mostrarExitoVenta === 'function') {
            mostrarExitoVenta();
        }
    }

    function cerrarModalFactura() {
        bootstrap.Modal.getInstance(document.getElementById('modalFactura')).hide();
        if (typeof mostrarExitoVenta === 'function') {
            mostrarExitoVenta();
        }
    }
</script>
