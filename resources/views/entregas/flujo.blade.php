@extends('layouts.app')

@section('title', 'Entregar Piezas - ' . ($orden->numero_orden ?? 'Orden'))

@section('content')
<div class="container-fluid py-4" x-data="entregaFlujo()">
    {{-- Page Header --}}
    <x-sinden.page-header title="Entregar Piezas" :description="'Orden ' . ($orden->numero_orden ?? '#' . $orden->id) . ' - ' . ($orden->cliente->nombre ?? 'Sin cliente')">
        <x-slot name="actions">
            <x-sinden.button variant="outline" icon="bi bi-arrow-left"
                href="{{ route('recepcion.entregas-pendientes') }}">Volver</x-sinden.button>
        </x-slot>
    </x-sinden.page-header>

    {{-- Resumen de la Orden --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <small class="text-muted d-block">Orden</small>
                    <strong>{{ $orden->numero_orden ?? 'Borrador' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Cliente</small>
                    <strong>{{ $orden->cliente->nombre ?? '-' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Fecha Entrega</small>
                    <strong>{{ $orden->fecha_entrega ? $orden->fecha_entrega->format('d/m/Y') : '-' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Piezas Pendientes</small>
                    <strong class="text-warning">{{ $piezasEntregables->count() }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Columna izquierda: Tabla de piezas --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-check2-square me-2 text-primary"></i>Piezas Pendientes
                        </h6>
                        <span class="text-muted small">
                            <span x-text="selectedIds.length"></span> de <span x-text="piezas.length"></span> seleccionada(s)
                        </span>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" @click="toggleAll()" :checked="allSelected">
                                    </th>
                                    <th>Identificador</th>
                                    <th class="text-center">Pendiente</th>
                                    <th class="text-center" style="width: 100px;">Entregar</th>
                                    <th>Material</th>
                                    <th>Calibre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="pieza in piezas" :key="pieza.id">
                                    <tr :class="selectedIds.includes(pieza.id) ? 'table-success' : ''">
                                        <td>
                                            <input type="checkbox" class="form-check-input" :value="pieza.id"
                                                :checked="selectedIds.includes(pieza.id)" @change="togglePieza(pieza.id)">
                                        </td>
                                        <td>
                                            <span class="fw-semibold" x-text="pieza.nombre"></span>
                                            <div class="small text-muted">
                                                <span x-text="pieza.cantidad_entregada"></span> / <span x-text="pieza.cantidad"></span> entregadas
                                            </div>
                                            <div class="progress mt-1" style="height: 4px; width: 100px;">
                                                <div class="progress-bar bg-info" :style="'width:' + (pieza.cantidad > 0 ? (pieza.cantidad_entregada / pieza.cantidad * 100) : 0) + '%'"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark" x-text="pieza.cantidad_pendiente"></span>
                                        </td>
                                        <td class="text-center" @click.stop>
                                            <input type="number" class="form-control form-control-sm text-center"
                                                :min="1" :max="pieza.cantidad_pendiente"
                                                x-model.number="cantidades[pieza.id]"
                                                @focus="if(!selectedIds.includes(pieza.id)) togglePieza(pieza.id)"
                                                style="width: 70px; margin: 0 auto;">
                                        </td>
                                        <td x-text="pieza.material || '-'"></td>
                                        <td x-text="pieza.calibre || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: Foto + Boton entregar --}}
        <div class="col-lg-4">
            {{-- Foto de entrega --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-camera me-2 text-primary"></i>Foto
                        <span class="text-muted fw-normal small ms-1">(Opcional)</span>
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    {{-- Sin foto y sin camara activa: boton para abrir camara --}}
                    <template x-if="!fotoSubida && !cameraActive && !uploading">
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary" @click="abrirCamara()">
                                <i class="bi bi-camera me-2"></i>Tomar Foto
                            </button>
                        </div>
                    </template>

                    {{-- Subiendo foto --}}
                    <template x-if="uploading">
                        <div class="text-center p-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <p class="mt-1 mb-0 text-muted small">Subiendo...</p>
                        </div>
                    </template>

                    {{-- Camara activa: video preview --}}
                    <template x-if="cameraActive && !fotoSubida">
                        <div class="text-center">
                            <video x-ref="cameraVideo" autoplay playsinline
                                class="img-fluid rounded shadow-sm" style="max-height: 250px; transform: scaleX(1);"></video>
                            <div class="mt-2 d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-success btn-sm" @click="capturarFoto()">
                                    <i class="bi bi-camera-fill me-1"></i>Capturar
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" @click="cerrarCamara()">
                                    <i class="bi bi-x-lg me-1"></i>Cancelar
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Foto tomada: preview --}}
                    <template x-if="fotoSubida">
                        <div class="text-center">
                            <img :src="fotoSubida.url" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                            <div class="mt-2 d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarFoto()">
                                    <i class="bi bi-trash me-1"></i>Quitar
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="quitarFoto(); $nextTick(() => abrirCamara())">
                                    <i class="bi bi-arrow-repeat me-1"></i>Retomar
                                </button>
                            </div>
                        </div>
                    </template>

                    <canvas x-ref="cameraCanvas" class="d-none"></canvas>
                </div>
            </div>

            {{-- Resumen de entrega --}}
            <div class="card border-0 shadow-sm mt-3" x-show="selectedIds.length > 0">
                <div class="card-body px-4 py-3">
                    <h6 class="fw-semibold mb-2"><i class="bi bi-receipt me-2 text-primary"></i>Resumen</h6>
                    <template x-for="pieza in piezasSeleccionadas" :key="pieza.id">
                        <div class="d-flex justify-content-between small mb-1">
                            <span x-text="pieza.nombre"></span>
                            <span class="fw-semibold" x-text="cantidades[pieza.id] + ' ud.'"></span>
                        </div>
                    </template>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small fw-bold">
                        <span>Total unidades</span>
                        <span x-text="totalUnidades"></span>
                    </div>
                </div>
            </div>

            {{-- Boton entregar --}}
            <div class="d-grid mt-3">
                <button class="btn btn-success btn-lg" :disabled="noneSelected || submitting" @click="confirmarEntrega()">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span x-show="!submitting">Entregar <span x-text="totalUnidades"></span> Unidad(es)</span>
                    <span x-show="submitting">Procesando...</span>
                </button>
            </div>

            {{-- Boton rapida: seleccionar todas --}}
            <div class="d-grid mt-2" x-show="piezas.length > 1 && !allSelected">
                <button class="btn btn-outline-primary btn-sm" @click="seleccionarTodas()">
                    <i class="bi bi-lightning me-1"></i>Seleccionar Todas
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function entregaFlujo() {
    var piezasData = @json($piezasEntregables);
    var cantidadesInit = {};
    piezasData.forEach(function(p) {
        cantidadesInit[p.id] = p.cantidad_pendiente;
    });

    return {
        piezas: piezasData,
        selectedIds: [],
        cantidades: cantidadesInit,
        fotoSubida: null,
        uploading: false,
        submitting: false,
        cameraActive: false,
        stream: null,

        get allSelected() {
            return this.selectedIds.length === this.piezas.length && this.piezas.length > 0;
        },

        get noneSelected() {
            return this.selectedIds.length === 0;
        },

        get piezasSeleccionadas() {
            var self = this;
            return this.piezas.filter(function(p) { return self.selectedIds.includes(p.id); });
        },

        get totalUnidades() {
            var self = this;
            var total = 0;
            this.selectedIds.forEach(function(id) {
                total += (self.cantidades[id] || 0);
            });
            return total;
        },

        toggleAll() {
            if (this.allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = this.piezas.map(function(p) { return p.id; });
            }
        },

        seleccionarTodas() {
            this.selectedIds = this.piezas.map(function(p) { return p.id; });
        },

        togglePieza(id) {
            var idx = this.selectedIds.indexOf(id);
            if (idx > -1) {
                this.selectedIds.splice(idx, 1);
            } else {
                this.selectedIds.push(id);
            }
        },

        abrirCamara() {
            var self = this;
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                Swal.fire('Error', 'Tu navegador no soporta acceso a la camara.', 'error');
                return;
            }
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function(mediaStream) {
                    self.stream = mediaStream;
                    self.cameraActive = true;
                    self.$nextTick(function() {
                        var video = self.$refs.cameraVideo;
                        if (video) {
                            video.srcObject = mediaStream;
                        }
                    });
                })
                .catch(function(err) {
                    console.error('Error al acceder a la camara:', err);
                    Swal.fire('Error', 'No se pudo acceder a la camara. Verifica los permisos.', 'error');
                });
        },

        capturarFoto() {
            var video = this.$refs.cameraVideo;
            var canvas = this.$refs.cameraCanvas;
            if (!video || !canvas) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            this.cerrarCamara();

            var self = this;
            canvas.toBlob(function(blob) {
                if (blob) {
                    self.subirBlob(blob);
                }
            }, 'image/jpeg', 0.85);
        },

        cerrarCamara() {
            if (this.stream) {
                this.stream.getTracks().forEach(function(track) { track.stop(); });
                this.stream = null;
            }
            this.cameraActive = false;
        },

        quitarFoto() {
            this.fotoSubida = null;
        },

        subirBlob(blob) {
            this.uploading = true;
            var formData = new FormData();
            formData.append('foto', blob, 'foto_entrega.jpg');

            var self = this;
            $.ajax({
                url: '{{ route("recepcion.entregas.foto-entrega", $orden) }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(data) {
                    if (data.success) {
                        self.fotoSubida = data.foto;
                    }
                    self.uploading = false;
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'No se pudo subir la foto.';
                    Swal.fire('Error', msg, 'error');
                    self.uploading = false;
                }
            });
        },

        confirmarEntrega() {
            if (this.noneSelected) return;
            var self = this;

            // Construir detalle de piezas
            var piezasPayload = [];
            var detalleHtml = '<ul class="text-start list-unstyled mb-0">';
            this.selectedIds.forEach(function(id) {
                var pieza = self.piezas.find(function(p) { return p.id === id; });
                var cant = self.cantidades[id] || 0;
                if (pieza && cant > 0) {
                    piezasPayload.push({ pieza_id: id, cantidad: cant });
                    detalleHtml += '<li><b>' + pieza.nombre + '</b>: ' + cant + ' de ' + pieza.cantidad + '</li>';
                }
            });
            detalleHtml += '</ul>';

            if (piezasPayload.length === 0) return;

            Swal.fire({
                title: 'Confirmar Entrega',
                html: detalleHtml,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4A7C59',
                confirmButtonText: 'Si, entregar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    self.submitting = true;

                    var payload = { piezas: piezasPayload };
                    if (self.fotoSubida) {
                        payload.foto_id = self.fotoSubida.id;
                    }

                    $.ajax({
                        url: '{{ route("recepcion.entregas.entregar", $orden) }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify(payload),
                        success: function(data) {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Entrega Exitosa',
                                    text: data.message,
                                    confirmButtonColor: '#4A7C59'
                                }).then(function() {
                                    window.location.href = '{{ route("recepcion.entregas-pendientes") }}';
                                });
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al procesar la entrega.';
                            Swal.fire('Error', msg, 'error');
                            self.submitting = false;
                        }
                    });
                }
            });
        }
    };
}
</script>
@endpush
