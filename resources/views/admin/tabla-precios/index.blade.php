@extends('layouts.app')

@section('title', 'Tabla de Precios')

@section('content')
<div class="container-fluid py-4" x-data="tablaPreciosApp()">
    {{-- Page Header --}}
    <x-sinden.page-header title="Tabla de Precios" description="Administracion de precios por servicio, calibre, largo y cantidad">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-file-earmark-excel" @click="exportarExcel()">
                Exportar Excel
            </x-sinden.button>
            <x-sinden.button variant="outline" icon="bi bi-upload" data-bs-toggle="modal" data-bs-target="#modalImport">
                Importar Excel
            </x-sinden.button>
            <x-sinden.button variant="primary" icon="bi bi-gear" data-bs-toggle="modal" data-bs-target="#modalServicios">
                Gestionar Servicios
            </x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <x-sinden.stat-card icon="bi bi-tags" :value="$totalServicios" title="Tipos de Servicio" color="primary" />
        <x-sinden.stat-card icon="bi bi-grid-3x3" :value="number_format($totalRegistros, 0, ',', '.')" title="Registros de Precios" color="info" />
        <x-sinden.stat-card icon="bi bi-clock" :value="$ultimaActualizacion ? \Carbon\Carbon::parse($ultimaActualizacion)->format('d/m/Y H:i') : 'N/A'" title="Ultima Actualizacion" color="secondary" />
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <h6 class="mb-0 fw-semibold text-dark">
                <i class="bi bi-funnel me-2 text-primary"></i>Seleccionar Servicio y Rango
            </h6>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Tipo de Servicio</label>
                    <select class="form-select" x-model="tipoServicio">
                        <option value="">-- Seleccione un servicio --</option>
                        @foreach($servicios as $servicio)
                        <option value="{{ $servicio->tipo_servicio }}">{{ $servicio->etiqueta_servicio }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Rango de Largo</label>
                    <select class="form-select" x-model="largoRangoIndex">
                        @foreach($largoRangos as $i => $rango)
                        <option value="{{ $i }}">{{ $rango->largo_rango_min }} - {{ $rango->largo_rango_max ?? '∞' }} mm</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100"
                        @click="cargarPrecios()"
                        :disabled="loading || !tipoServicio">
                        <span x-show="!loading"><i class="bi bi-arrow-clockwise me-2"></i>Cargar</span>
                        <span x-show="loading"><i class="bi bi-hourglass-split me-2"></i>Cargando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid de Precios --}}
    <div class="card border-0 shadow-sm mt-4" x-show="precios.length > 0" x-transition x-cloak>
        <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-table me-2 text-primary"></i><span x-text="servicioEtiqueta"></span>
                    </h6>
                    <small class="text-muted">
                        Precio minimo: <strong class="text-danger" x-text="'$' + formatNumber(precioMinimo)"></strong>
                        &bull; Rango largo seleccionado: <strong x-text="largoLabel"></strong>
                    </small>
                </div>
                <div>
                    <span class="badge bg-warning text-dark" x-show="Object.keys(modified).length > 0" x-cloak>
                        <i class="bi bi-pencil me-1"></i><span x-text="Object.keys(modified).length"></span> cambio(s) sin guardar
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" style="min-width: 700px;">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold" style="width: 120px;">Calibre</th>
                            <template x-for="rango in cantidadRangos" :key="rango.cantidad_rango_min">
                                <th class="text-center fw-semibold" x-text="rango.cantidad_rango_min + '-' + (rango.cantidad_rango_max ?? '∞')"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="calibre in calibres" :key="calibre.clave_calibre">
                            <tr>
                                <td class="fw-medium bg-light">
                                    <span x-text="calibre.clave_calibre"></span>
                                    <small class="text-muted d-block" x-text="calibre.calibre_mm + 'mm'"></small>
                                </td>
                                <template x-for="rango in cantidadRangos" :key="calibre.clave_calibre + '-' + rango.cantidad_rango_min">
                                    <td class="p-1 text-center">
                                        <input type="number"
                                            class="form-control form-control-sm text-end border-0"
                                            :class="{ 'bg-warning bg-opacity-25': isModified(calibre.clave_calibre, rango.cantidad_rango_min) }"
                                            :value="getPrecio(calibre.clave_calibre, rango.cantidad_rango_min)"
                                            @change="setPrecio(calibre.clave_calibre, rango.cantidad_rango_min, $event.target.value)"
                                            min="0"
                                            step="1"
                                            style="width: 100%; min-width: 90px;">
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3 gap-2">
                <button type="button" class="btn btn-outline-secondary"
                    @click="descartarCambios()"
                    x-show="Object.keys(modified).length > 0">
                    <i class="bi bi-x-circle me-1"></i>Descartar
                </button>
                <button type="button" class="btn btn-primary"
                    @click="guardarCambios()"
                    :disabled="guardando || Object.keys(modified).length === 0">
                    <span x-show="!guardando"><i class="bi bi-check-circle me-1"></i>Guardar Cambios</span>
                    <span x-show="guardando"><i class="bi bi-hourglass-split me-1"></i>Guardando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Mensaje si no se ha seleccionado --}}
    <div class="card border-0 shadow-sm mt-4" x-show="precios.length === 0 && !loading" x-cloak>
        <div class="card-body text-center py-5">
            <i class="bi bi-table text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">Seleccione un tipo de servicio y rango de largo, luego haga clic en <strong>Cargar</strong> para ver la tabla de precios.</p>
        </div>
    </div>

    {{-- ================ MODAL: Importar Excel ================ --}}
    <x-sinden.modal id="modalImport" title="Importar Precios desde Excel" size="md">
        <form id="formImport" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Archivo Excel (.xlsx, .xls)</label>
                <input type="file" class="form-control" name="archivo" accept=".xlsx,.xls" required>
                <small class="text-muted mt-1 d-block">
                    <i class="bi bi-info-circle me-1"></i>
                    El archivo debe tener las mismas columnas que el Excel exportado.
                    Solo se actualizaran precios de registros existentes.
                </small>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnImportar">
                    <i class="bi bi-upload me-1"></i>Importar
                </button>
            </div>
        </form>
    </x-sinden.modal>

    {{-- ================ MODAL: Gestionar Servicios ================ --}}
    <x-sinden.modal id="modalServicios" title="Gestionar Tipos de Servicio" size="lg">
        <div class="mb-4">
            <h6 class="fw-semibold mb-3"><i class="bi bi-plus-circle me-2 text-primary"></i>Agregar Nuevo Servicio</h6>
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" placeholder="Clave (ej: corte_laser)" x-model="nuevoServicio.clave">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" placeholder="Etiqueta (ej: CORTE LASER)" x-model="nuevoServicio.etiqueta">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm" placeholder="P. Minimo" x-model="nuevoServicio.precioMinimo" min="0">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary btn-sm w-100" @click="crearServicio()"
                        :disabled="!nuevoServicio.clave || !nuevoServicio.etiqueta || !nuevoServicio.precioMinimo">
                        <i class="bi bi-plus"></i> Agregar
                    </button>
                </div>
            </div>
        </div>

        <hr>

        <h6 class="fw-semibold mb-3"><i class="bi bi-list-ul me-2 text-primary"></i>Servicios Existentes</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Clave</th>
                        <th>Etiqueta</th>
                        <th class="text-end">Precio Min.</th>
                        <th class="text-center">Registros</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="srv in listaServicios" :key="srv.tipo_servicio">
                        <tr>
                            <td><code class="small" x-text="srv.tipo_servicio"></code></td>
                            <td>
                                <template x-if="editandoServicio === srv.tipo_servicio">
                                    <input type="text" class="form-control form-control-sm" x-model="editServicioData.etiqueta">
                                </template>
                                <template x-if="editandoServicio !== srv.tipo_servicio">
                                    <span x-text="srv.etiqueta_servicio"></span>
                                </template>
                            </td>
                            <td class="text-end">
                                <template x-if="editandoServicio === srv.tipo_servicio">
                                    <input type="number" class="form-control form-control-sm text-end" x-model="editServicioData.precioMinimo" min="0" style="width:120px;">
                                </template>
                                <template x-if="editandoServicio !== srv.tipo_servicio">
                                    <span x-text="'$' + formatNumber(srv.precio_minimo)"></span>
                                </template>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-muted border" x-text="srv.total_registros"></span>
                            </td>
                            <td class="text-end">
                                <template x-if="editandoServicio === srv.tipo_servicio">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-success" @click="guardarServicio(srv.tipo_servicio)"><i class="bi bi-check"></i></button>
                                        <button class="btn btn-outline-secondary" @click="editandoServicio = null"><i class="bi bi-x"></i></button>
                                    </div>
                                </template>
                                <template x-if="editandoServicio !== srv.tipo_servicio">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" @click="iniciarEdicionServicio(srv)" title="Editar"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-outline-danger" @click="eliminarServicio(srv.tipo_servicio, srv.etiqueta_servicio)" title="Eliminar"><i class="bi bi-trash"></i></button>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div x-show="listaServicios.length === 0" class="text-center text-muted py-3">
            <i class="bi bi-inbox"></i> No hay servicios registrados.
        </div>
    </x-sinden.modal>
</div>
@endsection

@push('scripts')
<script>
function tablaPreciosApp() {
    var largoRangosData = @json($largoRangos);

    return {
        // Filtros
        tipoServicio: '',
        largoRangoIndex: '0',
        loading: false,

        // Grid
        precios: [],
        calibres: [],
        cantidadRangos: [],
        servicioEtiqueta: '',
        precioMinimo: 0,
        modified: {},
        guardando: false,

        // Servicios modal
        listaServicios: [],
        nuevoServicio: { clave: '', etiqueta: '', precioMinimo: '' },
        editandoServicio: null,
        editServicioData: { etiqueta: '', precioMinimo: '' },

        get largoLabel() {
            var r = largoRangosData[this.largoRangoIndex];
            return r ? (r.largo_rango_min + ' - ' + (r.largo_rango_max ?? '∞') + ' mm') : '';
        },

        // ─── Grid ─────────────────────────────────────
        cargarPrecios() {
            if (!this.tipoServicio) return;
            this.loading = true;
            this.modified = {};
            var rango = largoRangosData[this.largoRangoIndex];

            $.ajax({
                url: '{{ route("admin.tabla-precios.index") }}',
                data: {
                    tipo_servicio: this.tipoServicio,
                    largo_min: rango.largo_rango_min,
                    largo_max: rango.largo_rango_max ?? '',
                },
                success: (data) => {
                    this.precios = data.precios;
                    this.calibres = data.calibres;
                    this.cantidadRangos = data.cantidad_rangos;
                    this.servicioEtiqueta = data.servicio_etiqueta;
                    this.precioMinimo = data.precio_minimo;
                    this.loading = false;
                },
                error: () => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los precios.' });
                    this.loading = false;
                }
            });
        },

        getPrecio(calibre, cantidadMin) {
            var p = this.precios.find(x => x.clave_calibre === calibre && x.cantidad_rango_min === cantidadMin);
            if (!p) return '';
            var key = p.id;
            return key in this.modified ? this.modified[key] : p.precio;
        },

        setPrecio(calibre, cantidadMin, valor) {
            var p = this.precios.find(x => x.clave_calibre === calibre && x.cantidad_rango_min === cantidadMin);
            if (!p) return;
            var nuevo = parseFloat(valor) || 0;
            if (parseFloat(p.precio) === nuevo) {
                delete this.modified[p.id];
            } else {
                this.modified[p.id] = nuevo;
            }
            // Force reactivity
            this.modified = Object.assign({}, this.modified);
        },

        isModified(calibre, cantidadMin) {
            var p = this.precios.find(x => x.clave_calibre === calibre && x.cantidad_rango_min === cantidadMin);
            return p && p.id in this.modified;
        },

        descartarCambios() {
            this.modified = {};
            this.cargarPrecios();
        },

        guardarCambios() {
            var cambios = Object.entries(this.modified).map(([id, precio]) => ({ id: parseInt(id), precio: precio }));
            if (cambios.length === 0) return;

            this.guardando = true;
            $.ajax({
                url: '{{ route("admin.tabla-precios.update") }}',
                method: 'POST',
                data: JSON.stringify({ _token: '{{ csrf_token() }}', precios: cambios }),
                contentType: 'application/json',
                success: (data) => {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                    this.modified = {};
                    this.cargarPrecios();
                    this.guardando = false;
                },
                error: (xhr) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'No se pudieron guardar los cambios.' });
                    this.guardando = false;
                }
            });
        },

        // ─── Servicios ────────────────────────────────
        cargarServicios() {
            $.get('{{ route("admin.tabla-precios.servicios") }}', (data) => {
                this.listaServicios = data;
            });
        },

        crearServicio() {
            $.ajax({
                url: '{{ route("admin.tabla-precios.servicios.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tipo_servicio: this.nuevoServicio.clave,
                    etiqueta_servicio: this.nuevoServicio.etiqueta,
                    precio_minimo: this.nuevoServicio.precioMinimo,
                },
                success: (data) => {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                    this.nuevoServicio = { clave: '', etiqueta: '', precioMinimo: '' };
                    this.cargarServicios();
                    // Recargar pagina para actualizar dropdowns
                    setTimeout(() => location.reload(), 1500);
                },
                error: (xhr) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'No se pudo crear el servicio.' });
                }
            });
        },

        iniciarEdicionServicio(srv) {
            this.editandoServicio = srv.tipo_servicio;
            this.editServicioData = { etiqueta: srv.etiqueta_servicio, precioMinimo: srv.precio_minimo };
        },

        guardarServicio(tipoServicio) {
            $.ajax({
                url: '/admin/tabla-precios/servicios/' + encodeURIComponent(tipoServicio),
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    etiqueta_servicio: this.editServicioData.etiqueta,
                    precio_minimo: this.editServicioData.precioMinimo,
                },
                success: (data) => {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                    this.editandoServicio = null;
                    this.cargarServicios();
                    setTimeout(() => location.reload(), 1500);
                },
                error: (xhr) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'No se pudo actualizar.' });
                }
            });
        },

        eliminarServicio(tipoServicio, etiqueta) {
            Swal.fire({
                title: 'Eliminar servicio?',
                html: 'Se eliminara <strong>' + etiqueta + '</strong> y todos sus registros de precios. Esta accion no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/tabla-precios/servicios/' + encodeURIComponent(tipoServicio),
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: (data) => {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                            this.cargarServicios();
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: (xhr) => {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'No se pudo eliminar.' });
                        }
                    });
                }
            });
        },

        // ─── Export / Import ──────────────────────────
        exportarExcel() {
            var url = '{{ route("admin.tabla-precios.export") }}';
            if (this.tipoServicio) url += '?tipo_servicio=' + encodeURIComponent(this.tipoServicio);
            window.location.href = url;
        },

        // ─── Helpers ──────────────────────────────────
        formatNumber(val) {
            var num = parseFloat(val) || 0;
            return num.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        init() {
            // Cargar servicios cuando se abre el modal
            var self = this;
            var modal = document.getElementById('modalServicios');
            if (modal) {
                modal.addEventListener('show.bs.modal', function() {
                    self.cargarServicios();
                });
            }
        }
    };
}

// Import Excel form
$(function() {
    $('#formImport').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var btn = $('#btnImportar');
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Importando...');

        $.ajax({
            url: '{{ route("admin.tabla-precios.import") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                bootstrap.Modal.getInstance(document.getElementById('modalImport')).hide();
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Error al importar el archivo.' });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-upload me-1"></i>Importar');
            }
        });
    });
});
</script>
@endpush
