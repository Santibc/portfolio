<x-app-layout>
    @section('title', 'Configuración SIIGO')

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('pdv.dashboard') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold mb-0"><i class="bi bi-cloud-arrow-up me-2"></i>Configuración SIIGO - Facturación Electrónica</h4>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('pdv.siigo.config.guardar') }}" method="POST" id="formSiigoConfig">
                    @csrf
                    <div class="row">
                        {{-- Columna izquierda: Credenciales y conexión --}}
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-key me-2"></i>Credenciales API SIIGO</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="siigo_activo" name="siigo_activo"
                                                {{ $config['siigo_activo'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="siigo_activo">
                                                Facturación SIIGO Activa
                                            </label>
                                        </div>
                                        <small class="text-muted">Activa/desactiva la integración con SIIGO</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Modo</label>
                                        <select name="siigo_modo" class="form-select">
                                            <option value="test" {{ $config['siigo_modo'] === 'test' ? 'selected' : '' }}>Test (Pruebas)</option>
                                            <option value="produccion" {{ $config['siigo_modo'] === 'produccion' ? 'selected' : '' }}>Producción</option>
                                        </select>
                                        <small class="text-muted">Usar modo Test para pruebas con SIIGO</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Usuario API (Email)</label>
                                        <input type="email" name="siigo_username" class="form-control"
                                            value="{{ $config['siigo_username'] }}" placeholder="usuario@empresa.com">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Access Key</label>
                                        <div class="input-group">
                                            <input type="password" name="siigo_access_key" class="form-control" id="inputAccessKey"
                                                value="{{ $config['siigo_access_key'] }}" placeholder="Access Key de SIIGO">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                                <i class="bi bi-eye" id="iconPassword"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Partner ID</label>
                                        <input type="text" name="siigo_partner_id" class="form-control"
                                            value="{{ $config['siigo_partner_id'] }}" placeholder="MiraclePdV">
                                        <small class="text-muted">Identificador de la integración para SIIGO</small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary" id="btnTestConexion">
                                            <i class="bi bi-wifi me-1"></i>Test Conexión
                                        </button>
                                        <span id="testResultado" class="align-self-center"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Opciones generales --}}
                            <div class="card border-0 shadow-sm mt-4">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-sliders me-2"></i>Opciones Generales</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="siigo_facturar_siempre"
                                                name="siigo_facturar_siempre" {{ $config['siigo_facturar_siempre'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="siigo_facturar_siempre">
                                                Facturar automáticamente todas las ventas
                                            </label>
                                        </div>
                                        <small class="text-muted">Si está activo, todas las ventas generan factura sin preguntar</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">NIT Consumidor Final</label>
                                        <input type="text" name="siigo_consumidor_final_nit" class="form-control"
                                            value="{{ $config['siigo_consumidor_final_nit'] }}">
                                        <small class="text-muted">NIT para ventas sin identificación del cliente (por defecto: 222222222222)</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Máximo reintentos</label>
                                        <input type="number" name="siigo_max_reintentos" class="form-control" min="1" max="10"
                                            value="{{ $config['siigo_max_reintentos'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Columna derecha: Catálogos SIIGO --}}
                        <div class="col-md-6 mb-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i>Catálogos SIIGO</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnCargarCatalogos">
                                        <i class="bi bi-arrow-repeat me-1"></i>Cargar de SIIGO
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="catalogosLoading" class="text-center py-3 d-none">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <span class="ms-2">Cargando catálogos de SIIGO...</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tipo de Documento - Factura de Venta</label>
                                        <select name="siigo_document_type_id" class="form-select" id="selectDocumentType">
                                            <option value="">-- Seleccione --</option>
                                            @if($config['siigo_document_type_id'])
                                                <option value="{{ $config['siigo_document_type_id'] }}" selected>
                                                    ID: {{ $config['siigo_document_type_id'] }} (guardado)
                                                </option>
                                            @endif
                                        </select>
                                        <small class="text-muted">Tipo de documento para facturas de venta en SIIGO</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tipo de Documento - Nota Crédito</label>
                                        <select name="siigo_credit_note_type_id" class="form-select" id="selectCreditNoteType">
                                            <option value="">-- Seleccione --</option>
                                            @if($config['siigo_credit_note_type_id'])
                                                <option value="{{ $config['siigo_credit_note_type_id'] }}" selected>
                                                    ID: {{ $config['siigo_credit_note_type_id'] }} (guardado)
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Método de Pago</label>
                                        <select name="siigo_payment_type_id" class="form-select" id="selectPaymentType">
                                            <option value="">-- Seleccione --</option>
                                            @if($config['siigo_payment_type_id'])
                                                <option value="{{ $config['siigo_payment_type_id'] }}" selected>
                                                    ID: {{ $config['siigo_payment_type_id'] }} (guardado)
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Impuesto IVA</label>
                                        <select name="siigo_tax_id" class="form-select" id="selectTax">
                                            <option value="">-- Seleccione (o dejar vacío si no aplica IVA) --</option>
                                            @if($config['siigo_tax_id'])
                                                <option value="{{ $config['siigo_tax_id'] }}" selected>
                                                    ID: {{ $config['siigo_tax_id'] }} (guardado)
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Vendedor en SIIGO</label>
                                        <select name="siigo_seller_id" class="form-select" id="selectSeller">
                                            <option value="">-- Seleccione --</option>
                                            @if($config['siigo_seller_id'])
                                                <option value="{{ $config['siigo_seller_id'] }}" selected>
                                                    ID: {{ $config['siigo_seller_id'] }} (guardado)
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-4">
                        <button type="submit" class="btn text-white px-4" style="background: var(--miracle-pink);">
                            <i class="bi bi-check-lg me-1"></i>Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePassword() {
            const input = document.getElementById('inputAccessKey');
            const icon = document.getElementById('iconPassword');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        document.getElementById('btnTestConexion').addEventListener('click', function() {
            const btn = this;
            const resultado = document.getElementById('testResultado');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Probando...';
            resultado.innerHTML = '';

            fetch('{{ route("pdv.siigo.test") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-wifi me-1"></i>Test Conexión';
                if (data.exito) {
                    resultado.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>' + data.mensaje + '</span>';
                } else {
                    resultado.innerHTML = '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>' + data.mensaje + '</span>';
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-wifi me-1"></i>Test Conexión';
                resultado.innerHTML = '<span class="badge bg-danger">Error de conexión</span>';
            });
        });

        document.getElementById('btnCargarCatalogos').addEventListener('click', function() {
            const btn = this;
            const loading = document.getElementById('catalogosLoading');
            btn.disabled = true;
            loading.classList.remove('d-none');

            fetch('{{ route("pdv.siigo.catalogos") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                loading.classList.add('d-none');

                if (!data.exito) {
                    alert(data.mensaje || 'Error al cargar catálogos');
                    return;
                }

                // Populate Document Types (FV)
                const selectDoc = document.getElementById('selectDocumentType');
                const currentDoc = selectDoc.value;
                selectDoc.innerHTML = '<option value="">-- Seleccione --</option>';
                if (Array.isArray(data.document_types)) {
                    data.document_types.forEach(dt => {
                        const opt = new Option(dt.id + ' - ' + (dt.name || dt.description || ''), dt.id);
                        if (dt.id == currentDoc) opt.selected = true;
                        selectDoc.add(opt);
                    });
                }

                // Populate Credit Note Types
                const selectCN = document.getElementById('selectCreditNoteType');
                const currentCN = selectCN.value;
                selectCN.innerHTML = '<option value="">-- Seleccione --</option>';
                if (Array.isArray(data.credit_note_types)) {
                    data.credit_note_types.forEach(dt => {
                        const opt = new Option(dt.id + ' - ' + (dt.name || dt.description || ''), dt.id);
                        if (dt.id == currentCN) opt.selected = true;
                        selectCN.add(opt);
                    });
                }

                // Populate Payment Types
                const selectPay = document.getElementById('selectPaymentType');
                const currentPay = selectPay.value;
                selectPay.innerHTML = '<option value="">-- Seleccione --</option>';
                if (Array.isArray(data.payment_types)) {
                    data.payment_types.forEach(pt => {
                        const opt = new Option(pt.id + ' - ' + (pt.name || ''), pt.id);
                        if (pt.id == currentPay) opt.selected = true;
                        selectPay.add(opt);
                    });
                }

                // Populate Taxes
                const selectTax = document.getElementById('selectTax');
                const currentTax = selectTax.value;
                selectTax.innerHTML = '<option value="">-- Seleccione (o dejar vacío si no aplica IVA) --</option>';
                if (Array.isArray(data.taxes)) {
                    data.taxes.forEach(t => {
                        const label = t.id + ' - ' + (t.name || '') + (t.percentage ? ' (' + t.percentage + '%)' : '');
                        const opt = new Option(label, t.id);
                        if (t.id == currentTax) opt.selected = true;
                        selectTax.add(opt);
                    });
                }

                // Populate Sellers
                const selectSeller = document.getElementById('selectSeller');
                const currentSeller = selectSeller.value;
                selectSeller.innerHTML = '<option value="">-- Seleccione --</option>';
                const sellersList = data.sellers?.results || (Array.isArray(data.sellers) ? data.sellers : []);
                sellersList.forEach(s => {
                    const name = s.first_name ? (s.first_name + ' ' + (s.last_name || '')) : (s.username || s.identification || '');
                    const opt = new Option(s.id + ' - ' + name, s.id);
                    if (s.id == currentSeller) opt.selected = true;
                    selectSeller.add(opt);
                });
            })
            .catch(err => {
                btn.disabled = false;
                loading.classList.add('d-none');
                alert('Error al cargar catálogos: ' + err.message);
            });
        });
    </script>
    @endpush
</x-app-layout>
