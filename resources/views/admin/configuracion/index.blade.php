@extends('layouts.app')

@section('title', 'Configuracion del Sistema')

@section('content')
<div class="container-fluid py-4" x-data="configuracionApp()">
    {{-- Page Header --}}
    <x-sinden.page-header title="Configuracion del Sistema" description="Parametros generales, empresa y timeouts">
        <x-slot name="actions">
            <x-sinden.button variant="primary" icon="bi bi-check-lg" @click="guardarTodo()" ::disabled="guardando">
                <span x-show="!guardando">Guardar Cambios</span>
                <span x-show="guardando"><i class="bi bi-hourglass-split me-1"></i>Guardando...</span>
            </x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-gear" :value="$configs->count()" title="Configuraciones" color="primary" />
        <x-sinden.stat-card icon="bi bi-people" :value="\App\Models\User::count()" title="Usuarios" color="info" />
        <x-sinden.stat-card icon="bi bi-shield-check" value="4" title="Roles" color="warning" />
        <x-sinden.stat-card icon="bi bi-key" value="29" title="Permisos" color="success" />
    </div>

    {{-- Secciones --}}
    <div class="mt-4">

        {{-- ═══ SECCION 1: EMPRESA ═══ --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#seccion-empresa" role="button">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-building me-2 text-primary"></i>Empresa
                    <i class="bi bi-chevron-down float-end transition-transform"></i>
                </h6>
            </div>
            <div class="collapse show" id="seccion-empresa">
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nombre de la Empresa</label>
                            <input type="text" class="form-control" x-model="nombre_empresa" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">NIT</label>
                            <input type="text" class="form-control" x-model="nit_empresa" maxlength="50" placeholder="Ej: 900.123.456-7">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Direccion</label>
                            <input type="text" class="form-control" x-model="direccion_empresa" maxlength="500">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefono</label>
                            <input type="text" class="form-control" x-model="telefono_empresa" maxlength="50">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium">Logo de la Empresa</label>
                            <div class="d-flex align-items-start gap-3">
                                <div x-show="logo_empresa" class="border rounded p-2" style="min-width:100px;">
                                    <img :src="logo_empresa" alt="Logo" style="max-height:80px;max-width:200px;" class="d-block">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" accept="image/png,image/jpeg,image/svg+xml,image/webp" @change="subirLogo($event)">
                                    <small class="text-muted">PNG, JPG, SVG o WEBP. Maximo 2MB.</small>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm" x-show="logo_empresa" @click="eliminarLogo()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ SECCION 2: FINANCIERO ═══ --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#seccion-financiero" role="button">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-cash-coin me-2 text-success"></i>Financiero
                    <i class="bi bi-chevron-down float-end"></i>
                </h6>
            </div>
            <div class="collapse show" id="seccion-financiero">
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">IVA por Defecto</label>
                            <div class="input-group">
                                <input type="number" class="form-control" x-model="porcentaje_iva_defecto" step="0.01" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Numeros Nequi para Pagos</label>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <template x-for="(num, i) in numeros_nequi" :key="i">
                                    <span class="badge bg-primary-subtle text-primary d-inline-flex align-items-center gap-1 px-3 py-2 fs-7">
                                        <span x-text="num"></span>
                                        <button type="button" class="btn-close btn-close-sm ms-1" style="font-size:.6em" @click="quitarNequi(i)"></button>
                                    </span>
                                </template>
                            </div>
                            <div class="input-group input-group-sm" style="max-width:300px;">
                                <input type="text" class="form-control" x-model="nuevoNequi" placeholder="Nuevo numero" maxlength="20" @keydown.enter.prevent="agregarNequi()">
                                <button class="btn btn-outline-primary" type="button" @click="agregarNequi()"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ SECCION 3: SISTEMA / OPERARIO ═══ --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#seccion-sistema" role="button">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2 text-info"></i>Sistema / Operario
                    <i class="bi bi-chevron-down float-end"></i>
                </h6>
            </div>
            <div class="collapse show" id="seccion-sistema">
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Autoguardado recepcion</label>
                            <div class="input-group">
                                <input type="number" class="form-control" x-model="timeout_autoguardado_recepcion" min="1" max="60">
                                <span class="input-group-text">minutos</span>
                            </div>
                            <small class="text-muted">Si recepcion deja de interactuar con el wizard de ordenes durante este tiempo, se guarda automaticamente como borrador.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Forzar cierre de orden</label>
                            <div class="input-group">
                                <input type="number" class="form-control" x-model="timeout_forzar_cierre" min="10" max="600">
                                <span class="input-group-text">segundos</span>
                            </div>
                            <small class="text-muted">Cuando un admin necesita una orden que un operario tiene bloqueada, el operario ve una cuenta regresiva de este tiempo antes de ser desconectado.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ SECCION 4: BORRADORES ═══ --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#seccion-borradores" role="button">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-file-earmark-text me-2 text-warning"></i>Borradores
                    <i class="bi bi-chevron-down float-end"></i>
                </h6>
            </div>
            <div class="collapse show" id="seccion-borradores">
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Dias para expirar borradores</label>
                            <div class="input-group">
                                <input type="number" class="form-control" x-model="dias_expiracion_borradores" min="1" max="365">
                                <span class="input-group-text">dias</span>
                            </div>
                            <small class="text-muted">Borradores mas antiguos que estos dias seran eliminados automaticamente.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Dias para borradores recientes</label>
                            <div class="input-group">
                                <input type="number" class="form-control" x-model="dias_borradores_recientes" min="1" max="90">
                                <span class="input-group-text">dias</span>
                            </div>
                            <small class="text-muted">Cantidad de dias para mostrar borradores en la lista de recientes.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ SECCION 5: CATALOGOS ═══ --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#seccion-catalogos" role="button">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-tags me-2 text-danger"></i>Catalogos
                    <i class="bi bi-chevron-down float-end"></i>
                </h6>
            </div>
            <div class="collapse show" id="seccion-catalogos">
                <div class="card-body px-4 pb-4 pt-2">
                    {{-- Materiales --}}
                    <div class="mb-4">
                        <label class="form-label fw-medium">Materiales Disponibles</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <template x-for="(mat, i) in materiales_disponibles" :key="i">
                                <span class="badge bg-secondary-subtle text-secondary d-inline-flex align-items-center gap-1 px-3 py-2 fs-7">
                                    <span x-text="mat"></span>
                                    <button type="button" class="btn-close btn-close-sm ms-1" style="font-size:.6em" @click="quitarMaterial(i)"></button>
                                </span>
                            </template>
                        </div>
                        <div class="input-group input-group-sm" style="max-width:350px;">
                            <input type="text" class="form-control" x-model="nuevoMaterial" placeholder="Nuevo material" maxlength="100" @keydown.enter.prevent="agregarMaterial()">
                            <button class="btn btn-outline-secondary" type="button" @click="agregarMaterial()"><i class="bi bi-plus"></i> Agregar</button>
                        </div>
                    </div>

                    {{-- Calibres --}}
                    <div>
                        <label class="form-label fw-medium">Calibres Disponibles</label>
                        <div class="table-responsive" style="max-width:500px;">
                            <table class="table table-sm table-bordered align-middle mb-2">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:45%">Calibre</th>
                                        <th style="width:35%">mm</th>
                                        <th style="width:20%" class="text-center">Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(cal, i) in calibres_disponibles" :key="i">
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm" x-model="cal.calibre" maxlength="20"></td>
                                            <td><input type="number" class="form-control form-control-sm" x-model="cal.mm" step="0.01" min="0"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm" @click="quitarCalibre(i)"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="input-group input-group-sm" style="max-width:500px;">
                            <input type="text" class="form-control" x-model="nuevoCalibre.calibre" placeholder="Calibre (ej: #20)" maxlength="20" @keydown.enter.prevent="agregarCalibre()">
                            <input type="number" class="form-control" x-model="nuevoCalibre.mm" placeholder="mm" step="0.01" min="0" @keydown.enter.prevent="agregarCalibre()">
                            <button class="btn btn-outline-secondary" type="button" @click="agregarCalibre()"><i class="bi bi-plus"></i> Agregar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ SECCION 6: OTROS ═══ --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#seccion-otros" role="button">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-sliders me-2 text-secondary"></i>Otros
                    <i class="bi bi-chevron-down float-end"></i>
                </h6>
            </div>
            <div class="collapse show" id="seccion-otros">
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cliente Predeterminado (Mostrador)</label>
                            <select class="form-select" x-model="cliente_predeterminado_id">
                                <option value="">-- Ninguno --</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Cliente que se selecciona automaticamente al crear una nueva orden.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Boton Guardar al final --}}
        <div class="text-end mt-4 mb-4">
            <x-sinden.button variant="primary" icon="bi bi-check-lg" size="lg" @click="guardarTodo()" ::disabled="guardando">
                <span x-show="!guardando">Guardar Todos los Cambios</span>
                <span x-show="guardando"><i class="bi bi-hourglass-split me-1"></i>Guardando...</span>
            </x-sinden.button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function configuracionApp() {
    return {
        guardando: false,

        // Empresa
        nombre_empresa: @json($configs['nombre_empresa']->valor ?? ''),
        logo_empresa: @json($configs['logo_empresa']->valor ?? ''),
        direccion_empresa: @json($configs['direccion_empresa']->valor ?? ''),
        telefono_empresa: @json($configs['telefono_empresa']->valor ?? ''),
        nit_empresa: @json($configs['nit_empresa']->valor ?? ''),

        // Financiero
        porcentaje_iva_defecto: @json($configs['porcentaje_iva_defecto']->valor ?? '19.00'),
        numeros_nequi: @json(json_decode($configs['numeros_nequi']->valor ?? '[]', true) ?: []),
        nuevoNequi: '',

        // Sistema
        timeout_autoguardado_recepcion: @json($configs['timeout_autoguardado_recepcion']->valor ?? '5'),
        timeout_forzar_cierre: @json($configs['timeout_forzar_cierre']->valor ?? '60'),

        // Borradores
        dias_expiracion_borradores: @json($configs['dias_expiracion_borradores']->valor ?? '30'),
        dias_borradores_recientes: @json($configs['dias_borradores_recientes']->valor ?? '7'),

        // Catalogos
        materiales_disponibles: @json(json_decode($configs['materiales_disponibles']->valor ?? '[]', true) ?: []),
        nuevoMaterial: '',
        calibres_disponibles: @json(json_decode($configs['calibres_disponibles']->valor ?? '[]', true) ?: []),
        nuevoCalibre: { calibre: '', mm: '' },

        // Otros
        cliente_predeterminado_id: @json($configs['cliente_predeterminado_id']->valor ?? ''),

        // ─── Tags: Nequi ─────────────────────────────
        agregarNequi() {
            var v = this.nuevoNequi.trim();
            if (v && !this.numeros_nequi.includes(v)) {
                this.numeros_nequi.push(v);
                this.nuevoNequi = '';
            }
        },
        quitarNequi(i) {
            this.numeros_nequi.splice(i, 1);
        },

        // ─── Tags: Materiales ────────────────────────
        agregarMaterial() {
            var v = this.nuevoMaterial.trim();
            if (v && !this.materiales_disponibles.includes(v)) {
                this.materiales_disponibles.push(v);
                this.nuevoMaterial = '';
            }
        },
        quitarMaterial(i) {
            this.materiales_disponibles.splice(i, 1);
        },

        // ─── Tabla: Calibres ─────────────────────────
        agregarCalibre() {
            var cal = this.nuevoCalibre.calibre.trim();
            var mm = parseFloat(this.nuevoCalibre.mm);
            if (cal && !isNaN(mm) && mm > 0) {
                this.calibres_disponibles.push({ calibre: cal, mm: mm });
                this.nuevoCalibre = { calibre: '', mm: '' };
            }
        },
        quitarCalibre(i) {
            this.calibres_disponibles.splice(i, 1);
        },

        // ─── Logo ────────────────────────────────────
        subirLogo(event) {
            var file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'El archivo no debe superar 2MB.' });
                event.target.value = '';
                return;
            }

            var formData = new FormData();
            formData.append('logo', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("admin.configuracion.upload-logo") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (data) => {
                    this.logo_empresa = data.path + '?t=' + Date.now();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                },
                error: (xhr) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'No se pudo subir el logo.' });
                }
            });
            event.target.value = '';
        },

        eliminarLogo() {
            Swal.fire({
                title: 'Eliminar logo?',
                text: 'Se eliminara el logo de la empresa.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.configuracion.delete-logo") }}',
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: (data) => {
                            this.logo_empresa = '';
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                        },
                        error: (xhr) => {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'No se pudo eliminar el logo.' });
                        }
                    });
                }
            });
        },

        // ─── Guardar Todo ────────────────────────────
        guardarTodo() {
            this.guardando = true;

            var payload = {
                configs: {
                    nombre_empresa: this.nombre_empresa,
                    direccion_empresa: this.direccion_empresa || '',
                    telefono_empresa: this.telefono_empresa || '',
                    nit_empresa: this.nit_empresa || '',
                    porcentaje_iva_defecto: parseFloat(this.porcentaje_iva_defecto) || 19,
                    numeros_nequi: this.numeros_nequi,
                    timeout_autoguardado_recepcion: parseInt(this.timeout_autoguardado_recepcion) || 5,
                    timeout_forzar_cierre: parseInt(this.timeout_forzar_cierre) || 60,
                    dias_expiracion_borradores: parseInt(this.dias_expiracion_borradores) || 30,
                    dias_borradores_recientes: parseInt(this.dias_borradores_recientes) || 7,
                    materiales_disponibles: this.materiales_disponibles,
                    calibres_disponibles: this.calibres_disponibles.map(c => ({ calibre: c.calibre, mm: parseFloat(c.mm) })),
                    cliente_predeterminado_id: this.cliente_predeterminado_id || null,
                }
            };

            $.ajax({
                url: '{{ route("admin.configuracion.update") }}',
                method: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: (data) => {
                    this.guardando = false;
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                },
                error: (xhr) => {
                    this.guardando = false;
                    var msg = 'Error al guardar las configuraciones.';
                    if (xhr.responseJSON?.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            });
        }
    };
}
</script>
@endpush
