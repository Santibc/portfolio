<x-app-layout>
    <x-slot name="header">Orden de Servicio #{{ $orden->numero_orden }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row mb-4">
                <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('st.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('st.ordenes.index') }}">Órdenes</a></li>
                    <li class="breadcrumb-item active">{{ $orden->numero_orden }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCambiarEstado">
                <i class="bi bi-arrow-repeat"></i> Cambiar Estado
            </button>
            <a href="{{ route('st.ordenes.edit', $orden) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('st.ordenes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            {{-- Información de la orden --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información de la Orden</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>N° Orden:</strong> {{ $orden->numero_orden }}
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong>
                            @php
                                $estadoBadge = [
                                    'recibida' => 'secondary',
                                    'asignada' => 'info',
                                    'en_proceso' => 'primary',
                                    'pendiente_repuestos' => 'warning',
                                    'completada' => 'success',
                                    'entregada' => 'success',
                                    'cancelada' => 'danger'
                                ];
                            @endphp
                            <span class="badge bg-{{ $estadoBadge[$orden->estado] ?? 'secondary' }} fs-6">
                                {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Cliente:</strong> {{ $orden->cliente->nombre_completo ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Equipo:</strong>
                            @if($orden->equipo)
                                {{ $orden->equipo->marca }} {{ $orden->equipo->modelo }} - S/N: {{ $orden->equipo->numero_serie }}
                            @else
                                Sin equipo específico
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Técnico Asignado:</strong> {{ $orden->tecnico->nombre_completo ?? 'Sin asignar' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Tipo Servicio:</strong> {{ $orden->tipo_servicio }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Prioridad:</strong>
                            @php
                                $prioridadBadge = ['baja' => 'secondary', 'media' => 'info', 'alta' => 'warning', 'urgente' => 'danger'];
                            @endphp
                            <span class="badge bg-{{ $prioridadBadge[$orden->prioridad] }}">{{ ucfirst($orden->prioridad) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Días transcurridos:</strong> {{ $orden->dias_transcurridos }} días
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Descripción del Problema:</strong>
                        <p class="mb-0">{{ $orden->descripcion_problema }}</p>
                    </div>
                    @if($orden->accesorios_entregados)
                        <div class="mb-3">
                            <strong>Accesorios Entregados:</strong>
                            <p class="mb-0">{{ $orden->accesorios_entregados }}</p>
                        </div>
                    @endif
                    @if($orden->observaciones)
                        <div class="mb-3">
                            <strong>Observaciones:</strong>
                            <p class="mb-0">{{ $orden->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Diagnósticos --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Diagnósticos</h6>
                    <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalAgregarDiagnostico">
                        <i class="bi bi-plus-circle"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    @forelse($orden->diagnosticos as $diagnostico)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $diagnostico->tecnico->nombre_completo ?? 'N/A' }}</strong>
                                    @if($diagnostico->requiere_repuestos)
                                        <span class="badge bg-warning text-dark ms-2">Requiere Repuestos</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $diagnostico->fecha_diagnostico->format('d/m/Y') }}</small>
                            </div>

                            <div class="mt-2">
                                <p class="mb-2"><strong>Diagnóstico:</strong><br>{{ $diagnostico->diagnostico_tecnico }}</p>

                                @if($diagnostico->reparaciones_realizadas)
                                    <p class="mb-2"><strong>Reparaciones:</strong><br>{{ $diagnostico->reparaciones_realizadas }}</p>
                                @endif

                                @if($diagnostico->recomendaciones)
                                    <p class="mb-2"><strong>Recomendaciones:</strong><br>{{ $diagnostico->recomendaciones }}</p>
                                @endif

                                @if($diagnostico->repuestos_necesarios)
                                    <p class="mb-2"><strong>Repuestos Necesarios:</strong><br>{{ $diagnostico->repuestos_necesarios }}</p>
                                @endif

                                <div class="d-flex gap-3 mt-2">
                                    @if($diagnostico->tiempo_estimado_horas)
                                        <small class="text-muted"><i class="bi bi-clock"></i> {{ $diagnostico->tiempo_estimado_horas }}h</small>
                                    @endif
                                    @if($diagnostico->costo_estimado)
                                        <small class="text-muted"><i class="bi bi-currency-dollar"></i> ${{ number_format($diagnostico->costo_estimado, 2) }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay diagnósticos registrados</p>
                    @endforelse
                </div>
            </div>

            {{-- Repuestos Utilizados --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Repuestos Utilizados</h6>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalAgregarRepuesto">
                        <i class="bi bi-plus-circle"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    @if($orden->repuestosUsados->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Repuesto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orden->repuestosUsados as $repuestoUsado)
                                        <tr>
                                            <td>{{ $repuestoUsado->repuesto->nombre ?? 'N/A' }}</td>
                                            <td>{{ $repuestoUsado->cantidad }}</td>
                                            <td>${{ number_format($repuestoUsado->precio_unitario, 2) }}</td>
                                            <td>${{ number_format($repuestoUsado->cantidad * $repuestoUsado->precio_unitario, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No se han utilizado repuestos</p>
                    @endif
                </div>
            </div>

            {{-- Historial de Estados --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Estados</h6>
                </div>
                <div class="card-body">
                    @forelse($orden->historialEstados as $historial)
                        <div class="d-flex border-bottom pb-2 mb-2">
                            <div class="me-3">
                                <i class="bi bi-circle-fill text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong>{{ ucfirst(str_replace('_', ' ', $historial->estado)) }}</strong>
                                <p class="mb-0 small text-muted">
                                    {{ $historial->created_at->format('d/m/Y H:i') }} -
                                    {{ $historial->usuario->name ?? 'Sistema' }}
                                </p>
                                @if($historial->observaciones)
                                    <p class="mb-0 small">{{ $historial->observaciones }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay historial disponible</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Fechas --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-calendar me-2"></i>Fechas</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Recepción:</strong><br>{{ $orden->fecha_recepcion->format('d/m/Y') }}</p>
                    @if($orden->fecha_asignacion)
                        <p class="mb-2"><strong>Asignación:</strong><br>{{ $orden->fecha_asignacion->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($orden->fecha_promesa_entrega)
                        <p class="mb-2"><strong>Promesa Entrega:</strong><br>{{ $orden->fecha_promesa_entrega->format('d/m/Y') }}</p>
                    @endif
                    @if($orden->fecha_finalizacion)
                        <p class="mb-0"><strong>Finalización:</strong><br>{{ $orden->fecha_finalizacion->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>

            {{-- Costos --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Costos</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Mano de Obra:</strong> ${{ number_format($orden->costo_mano_obra ?? 0, 2) }}</p>
                    <p class="mb-2"><strong>Repuestos:</strong> ${{ number_format($orden->costo_repuestos ?? 0, 2) }}</p>
                    <hr>
                    <p class="mb-0"><strong>Total:</strong> <span class="fs-5 text-success">${{ number_format($orden->costo_total ?? 0, 2) }}</span></p>
                </div>
            </div>

            {{-- Imágenes --}}
            @if($orden->imagenes->count() > 0)
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-images me-2"></i>Imágenes</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($orden->imagenes as $imagen)
                            <div class="col-6">
                                <a href="/{{ $imagen->ruta_archivo }}" target="_blank">
                                    <img src="/{{ $imagen->ruta_archivo }}" class="img-thumbnail" alt="Imagen">
                                </a>
                                <small class="d-block text-muted">{{ ucfirst($imagen->tipo_imagen) }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            </div>
        </div>
        </div>
    </div>

{{-- Modal Cambiar Estado --}}
<div class="modal fade" id="modalCambiarEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Cambiar Estado de la Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCambiarEstado">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Estado Actual</label>
                        <input type="text" class="form-control" value="{{ ucfirst(str_replace('_', ' ', $orden->estado)) }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nuevo Estado <span class="text-danger">*</span></label>
                        <select name="nuevo_estado" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="recibida">Recibida</option>
                            <option value="asignada">Asignada</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="pendiente_repuestos">Pendiente Repuestos</option>
                            <option value="completada">Completada</option>
                            <option value="entregada">Entregada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Motivo del cambio de estado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Agregar Diagnóstico --}}
<div class="modal fade" id="modalAgregarDiagnostico" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Agregar Diagnóstico Técnico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('st.ordenes.diagnostico.store', $orden) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Técnico <span class="text-danger">*</span></label>
                            <select name="st_tecnico_id" class="form-select" required>
                                @if($orden->tecnico)
                                    <option value="{{ $orden->tecnico->id }}" selected>{{ $orden->tecnico->nombre_completo }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Diagnóstico Técnico <span class="text-danger">*</span></label>
                        <textarea name="diagnostico_tecnico" class="form-control" rows="4" required placeholder="Descripción detallada del diagnóstico y fallas encontradas..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reparaciones Realizadas</label>
                        <textarea name="reparaciones_realizadas" class="form-control" rows="3" placeholder="Descripción de las reparaciones efectuadas..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Recomendaciones</label>
                        <textarea name="recomendaciones" class="form-control" rows="2" placeholder="Recomendaciones para el cliente o futuras reparaciones..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tiempo Estimado (horas)</label>
                            <input type="number" name="tiempo_estimado_horas" class="form-control" step="0.5" min="0" placeholder="0.0">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Costo Estimado</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="costo_estimado" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block">Requiere Repuestos</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="requiere_repuestos" id="requiere_repuestos" value="1">
                                <label class="form-check-label" for="requiere_repuestos">Sí</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="repuestos_necesarios_div" style="display: none;">
                        <label class="form-label">Repuestos Necesarios</label>
                        <textarea name="repuestos_necesarios" class="form-control" rows="2" placeholder="Lista de repuestos que se necesitan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Guardar Diagnóstico</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Agregar Repuesto --}}
<div class="modal fade" id="modalAgregarRepuesto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Agregar Repuesto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('st.ordenes.repuesto.store', $orden) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Repuesto <span class="text-danger">*</span></label>
                        <select name="st_repuesto_id" id="repuestoSelect" class="form-select" required>
                            <option value="">Seleccione un repuesto...</option>
                            @foreach($repuestos as $repuesto)
                                <option value="{{ $repuesto->id }}"
                                        data-precio="{{ $repuesto->precio_venta ?? 0 }}"
                                        data-stock="{{ $repuesto->stock_actual }}">
                                    {{ $repuesto->nombre }} - Stock: {{ $repuesto->stock_actual }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad" id="cantidadRepuesto" class="form-control" min="1" value="1" required>
                        <small class="text-muted">Stock disponible: <span id="stockDisponible">-</span></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio Unitario <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="precio_unitario" id="precioUnitario" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" id="subtotalRepuesto" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Agregar Repuesto</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Cambiar estado
    $('#formCambiarEstado').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route('st.ordenes.cambiar-estado', $orden) }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    title: 'Éxito',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cambiar el estado',
                    icon: 'error'
                });
            }
        });
    });

    // Repuesto seleccionado
    $('#repuestoSelect').on('change', function() {
        const option = $(this).find(':selected');
        const precio = option.data('precio');
        const stock = option.data('stock');

        $('#precioUnitario').val(precio);
        $('#stockDisponible').text(stock);
        $('#cantidadRepuesto').attr('max', stock);
        calcularSubtotal();
    });

    // Calcular subtotal
    function calcularSubtotal() {
        const cantidad = parseFloat($('#cantidadRepuesto').val()) || 0;
        const precio = parseFloat($('#precioUnitario').val()) || 0;
        const subtotal = cantidad * precio;

        $('#subtotalRepuesto').val(subtotal.toFixed(2));
    }

    $('#cantidadRepuesto, #precioUnitario').on('input', calcularSubtotal);

    // Mostrar/ocultar campo de repuestos necesarios
    $('#requiere_repuestos').on('change', function() {
        if ($(this).is(':checked')) {
            $('#repuestos_necesarios_div').slideDown();
        } else {
            $('#repuestos_necesarios_div').slideUp();
        }
    });
});
</script>
@endpush
</x-app-layout>
