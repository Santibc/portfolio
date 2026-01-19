<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt fs-4"></i>
            <span>Seguimiento de Envío</span>
            <span class="badge bg-primary ms-2">{{ $solicitud->numero_solicitud }}</span>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            {{-- Alertas de sesión --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if(session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif

            {{-- Navegación --}}
            <div class="mb-4">
                <a href="{{ route('portal.pedido.detalle', $solicitud->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver al detalle
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    {{-- Timeline de estados --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Estado del Envío
                            </h5>
                        </div>
                        <div class="card-body">
                            <x-timeline-envio :solicitud="$solicitud" />
                        </div>
                    </div>

                    {{-- Información de la guía --}}
                    @if($solicitud->estaDespachado())
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-truck me-2"></i>
                                    Datos del Envío
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if($solicitud->transportadora)
                                            <div class="mb-3">
                                                <label class="text-muted small">Transportadora</label>
                                                <p class="mb-0 fs-5 fw-semibold">{{ $solicitud->transportadora }}</p>
                                            </div>
                                        @endif
                                        @if($solicitud->numero_guia)
                                            <div class="mb-3">
                                                <label class="text-muted small">Número de Guía</label>
                                                <p class="mb-0">
                                                    <code class="fs-5 bg-light p-2 rounded">{{ $solicitud->numero_guia }}</code>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($solicitud->despachado_en)
                                            <div class="mb-3">
                                                <label class="text-muted small">Fecha de Despacho</label>
                                                <p class="mb-0 fs-5">{{ $solicitud->despachado_en->format('d/m/Y H:i') }}</p>
                                            </div>
                                        @endif
                                        @if($solicitud->entregado_en)
                                            <div class="mb-3">
                                                <label class="text-muted small">Fecha de Entrega</label>
                                                <p class="mb-0 fs-5 text-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    {{ $solicitud->entregado_en->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($solicitud->puedeDescargarGuia())
                                    <hr>
                                    <a href="{{ route('portal.pedido.guia', $solicitud->id) }}"
                                       class="btn btn-success">
                                        <i class="bi bi-file-earmark-arrow-down me-2"></i>
                                        Descargar Guía de Envío
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Estado del pedido --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Resumen del Pedido
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center border-end">
                                    <label class="text-muted small d-block mb-1">Estado</label>
                                    <span class="badge bg-{{ $solicitud->color_estado }} fs-6">
                                        {{ ucfirst($solicitud->estado) }}
                                    </span>
                                </div>
                                <div class="col-md-4 text-center border-end">
                                    <label class="text-muted small d-block mb-1">Pago</label>
                                    <span class="badge bg-{{ $solicitud->color_estado_pago }} fs-6">
                                        {{ $solicitud->etiqueta_estado_pago }}
                                    </span>
                                </div>
                                <div class="col-md-4 text-center">
                                    <label class="text-muted small d-block mb-1">Monto Total</label>
                                    <span class="fs-5 fw-bold">${{ number_format($solicitud->monto_total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ayuda --}}
                    <div class="card border-0 shadow-sm mt-4 bg-light">
                        <div class="card-body">
                            <h6 class="mb-2">
                                <i class="bi bi-question-circle me-2"></i>
                                ¿Tienes preguntas sobre tu envío?
                            </h6>
                            <p class="text-muted mb-0 small">
                                Si tienes alguna consulta sobre el estado de tu pedido, no dudes en contactarnos.
                                Puedes escribirnos a <strong>ventas@miraclebeauty.com</strong> o llamarnos al
                                <strong>+57 300 123 4567</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
