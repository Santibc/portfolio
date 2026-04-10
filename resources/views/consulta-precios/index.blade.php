@extends('layouts.app')

@section('title', 'Consulta de Precios')

@section('content')
<div class="container-fluid py-4" x-data="consultaPrecios()">
    {{-- Page Header --}}
    <x-sinden.page-header title="Consulta de Precios" description="Consulta rapida de precios por servicio, calibre, largo de pieza (mm) y cantidad de servicios">
    </x-sinden.page-header>

    <div class="row justify-content-center mt-4">
        <div class="col-lg-8 col-xl-6">
            {{-- Formulario de consulta --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-calculator me-2 text-primary"></i>Parametros de Consulta
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Tipo de Servicio</label>
                            <select class="form-select" x-model="tipoServicio">
                                <option value="">-- Seleccione un servicio --</option>
                                @foreach($servicios as $servicio)
                                <option value="{{ $servicio->tipo_servicio }}">{{ $servicio->etiqueta_servicio }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Calibre</label>
                            <select class="form-select" x-model="claveCalibe">
                                <option value="">-- Calibre --</option>
                                @foreach($calibres as $calibre)
                                <option value="{{ $calibre['calibre'] }}">{{ $calibre['calibre'] }} ({{ $calibre['mm'] }}mm)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Largo de la pieza (mm)</label>
                            <input type="number" class="form-control" x-model="largoMm" placeholder="Ej: 150" min="0" step="1">
                            <small class="text-muted">Largo del corte o doblez en milimetros</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium">Cantidad de servicios</label>
                            <input type="number" class="form-control" x-model="cantidadServicios" placeholder="Ej: 25" min="1" step="1">
                            <small class="text-muted">Numero de piezas a procesar</small>
                        </div>

                        <div class="col-12">
                            <button type="button" class="btn btn-primary w-100"
                                @click="consultar()"
                                :disabled="consultando || !tipoServicio || !claveCalibe || !largoMm || !cantidadServicios">
                                <span x-show="!consultando"><i class="bi bi-search me-2"></i>Consultar Precio</span>
                                <span x-show="consultando"><i class="bi bi-hourglass-split me-2"></i>Consultando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resultado --}}
            <div class="card border-0 shadow-sm mt-4" x-show="resultado" x-transition x-cloak>
                <div class="card-body px-4 py-4 text-center">
                    <template x-if="resultado && resultado.encontrado">
                        <div>
                            <p class="text-muted mb-1" x-text="resultado.etiqueta_servicio"></p>
                            <p class="text-muted mb-3">
                                Calibre: <strong x-text="resultado.clave_calibre"></strong>
                                (<span x-text="resultado.calibre_mm"></span>mm)
                                &bull; Largo pieza: <span x-text="resultado.largo_rango"></span>
                                &bull; Cant. servicios: <span x-text="resultado.cantidad_rango"></span>
                            </p>
                            <div class="display-4 fw-bold text-primary mb-2" x-text="resultado.precio_formato"></div>
                            <p class="text-muted small">
                                Precio minimo del servicio: <span x-text="resultado.precio_minimo_formato"></span>
                            </p>
                        </div>
                    </template>
                    <template x-if="resultado && !resultado.encontrado">
                        <div>
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2.5rem;"></i>
                            <p class="mt-3 text-muted" x-text="resultado.mensaje"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Error --}}
            <div class="alert alert-danger mt-4" x-show="error" x-transition x-cloak>
                <i class="bi bi-x-circle me-2"></i><span x-text="error"></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function consultaPrecios() {
    return {
        tipoServicio: '',
        claveCalibe: '',
        largoMm: '',
        cantidadServicios: '',
        resultado: null,
        consultando: false,
        error: '',

        consultar() {
            if (!this.tipoServicio || !this.claveCalibe || !this.largoMm || !this.cantidadServicios) return;

            this.consultando = true;
            this.error = '';
            this.resultado = null;

            $.ajax({
                url: '{{ route("recepcion.consulta-precios.buscar") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tipo_servicio: this.tipoServicio,
                    clave_calibre: this.claveCalibe,
                    largo_mm: this.largoMm,
                    cantidad_servicios: this.cantidadServicios,
                },
                success: (data) => {
                    this.resultado = data;
                    this.consultando = false;
                },
                error: (xhr) => {
                    this.error = xhr.responseJSON?.message || 'Error al consultar el precio.';
                    this.consultando = false;
                }
            });
        }
    };
}
</script>
@endpush
