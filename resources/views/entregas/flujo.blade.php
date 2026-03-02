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
                    <small class="text-muted d-block">Piezas para Entregar</small>
                    <strong class="text-success">{{ $piezasEntregables->count() }}</strong>
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
                            <i class="bi bi-check2-square me-2 text-primary"></i>Piezas Listas
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
                                    <th>Nombre</th>
                                    <th class="text-center">Cant.</th>
                                    <th>Material</th>
                                    <th>Calibre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="pieza in piezas" :key="pieza.id">
                                    <tr :class="selectedIds.includes(pieza.id) ? 'table-success' : ''" style="cursor: pointer;" @click="togglePieza(pieza.id)">
                                        <td @click.stop>
                                            <input type="checkbox" class="form-check-input" :value="pieza.id"
                                                :checked="selectedIds.includes(pieza.id)" @change="togglePieza(pieza.id)">
                                        </td>
                                        <td class="fw-semibold" x-text="pieza.nombre"></td>
                                        <td class="text-center" x-text="pieza.cantidad"></td>
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
                    <template x-if="!fotoSubida">
                        <div class="text-center p-4 border border-2 border-dashed rounded-3"
                            style="cursor: pointer; border-color: #dee2e6 !important;"
                            @click="$refs.fileInput.click()"
                            @drop.prevent="handleDrop($event)"
                            @dragover.prevent
                            @dragenter.prevent>
                            <template x-if="!uploading">
                                <div>
                                    <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #adb5bd;"></i>
                                    <p class="mt-1 mb-0 text-muted small">Clic o arrastra imagen</p>
                                </div>
                            </template>
                            <template x-if="uploading">
                                <div>
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <p class="mt-1 mb-0 text-muted small">Subiendo...</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="fotoSubida">
                        <div class="text-center">
                            <img :src="fotoSubida.url" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="fotoSubida = null">
                                    <i class="bi bi-trash me-1"></i>Quitar
                                </button>
                            </div>
                        </div>
                    </template>

                    <input type="file" x-ref="fileInput" accept="image/*" class="d-none" @change="subirFoto($event)">
                </div>
            </div>

            {{-- Boton entregar --}}
            <div class="d-grid mt-3">
                <button class="btn btn-success btn-lg" :disabled="noneSelected || submitting" @click="confirmarEntrega()">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    <span x-show="!submitting">Entregar <span x-text="selectedIds.length"></span> Pieza(s)</span>
                    <span x-show="submitting">Procesando...</span>
                </button>
            </div>

            {{-- Boton rapida: entregar todas --}}
            <div class="d-grid mt-2" x-show="piezas.length > 1 && !allSelected">
                <button class="btn btn-outline-primary btn-sm" @click="toggleAll(); $nextTick(() => confirmarEntrega())">
                    <i class="bi bi-lightning me-1"></i>Entregar Todas
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function entregaFlujo() {
    return {
        piezas: @json($piezasEntregables),
        selectedIds: [],
        fotoSubida: null,
        uploading: false,
        submitting: false,

        get allSelected() {
            return this.selectedIds.length === this.piezas.length && this.piezas.length > 0;
        },

        get noneSelected() {
            return this.selectedIds.length === 0;
        },

        toggleAll() {
            if (this.allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = this.piezas.map(p => p.id);
            }
        },

        togglePieza(id) {
            var idx = this.selectedIds.indexOf(id);
            if (idx > -1) {
                this.selectedIds.splice(idx, 1);
            } else {
                this.selectedIds.push(id);
            }
        },

        handleDrop(event) {
            var file = event.dataTransfer.files[0];
            if (file) this.processFile(file);
        },

        subirFoto(event) {
            var file = event.target.files[0];
            if (file) this.processFile(file);
            event.target.value = '';
        },

        processFile(file) {
            if (!file.type.startsWith('image/')) {
                Swal.fire('Error', 'Solo se permiten imagenes.', 'error');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire('Error', 'La imagen no puede superar 5MB.', 'error');
                return;
            }

            this.uploading = true;
            var formData = new FormData();
            formData.append('foto', file);

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
            var count = this.selectedIds.length;

            Swal.fire({
                title: 'Confirmar Entrega',
                html: 'Se entregaran <b>' + count + '</b> pieza(s) al cliente.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4A7C59',
                confirmButtonText: 'Si, entregar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    self.submitting = true;

                    $.ajax({
                        url: '{{ route("recepcion.entregas.entregar", $orden) }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify({ pieza_ids: self.selectedIds }),
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
