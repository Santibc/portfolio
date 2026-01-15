@extends('layouts.app')

@section('title', 'Fichajes')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                @if($esTrabajador ?? false)
                    Mis Fichajes
                @else
                    Fichajes
                @endif
            </h1>
            <p class="text-muted mb-0">
                @if($esTrabajador ?? false)
                    Control de tus entradas y salidas
                @else
                    Control de entrada y salida de trabajadores
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @if(!($esTrabajador ?? false))
                <a href="{{ route('fichajes.resumen') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-bar-chart me-2"></i>Resumen Mensual
                </a>
            @endif

            @if($esTrabajador ?? false)
                {{-- Botón de fichar para trabajadores --}}
                @if($fichajeHoy && $fichajeHoy->hora_entrada && !$fichajeHoy->hora_salida)
                    {{-- Ya fichó entrada, mostrar botón de salida --}}
                    <button type="button" class="btn btn-danger" id="btnFicharSalida">
                        <i class="bi bi-box-arrow-right me-2"></i>Fichar Salida
                    </button>
                @elseif(!$fichajeHoy)
                    {{-- No ha fichado hoy --}}
                    <a href="{{ route('fichajes.create') }}" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Fichar Entrada
                    </a>
                @else
                    {{-- Ya completó el fichaje de hoy --}}
                    <span class="btn btn-success disabled">
                        <i class="bi bi-check-circle me-2"></i>Fichaje Completado
                    </span>
                @endif
            @else
                @can('crear_fichajes')
                <a href="{{ route('fichajes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Fichaje
                </a>
                @endcan
            @endif
        </div>
    </div>

    @if($esTrabajador ?? false)
        {{-- Alerta del estado de hoy para trabajadores --}}
        @if($fichajeHoy)
            <div class="alert {{ $fichajeHoy->hora_salida ? 'alert-success' : 'alert-info' }} mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi {{ $fichajeHoy->hora_salida ? 'bi-check-circle' : 'bi-clock' }} fs-4 me-3"></i>
                    <div>
                        <strong>Hoy {{ now()->format('d/m/Y') }}</strong><br>
                        @if($fichajeHoy->hora_entrada)
                            Entrada: <strong>{{ \Carbon\Carbon::parse($fichajeHoy->hora_entrada)->format('H:i') }}</strong>
                        @endif
                        @if($fichajeHoy->hora_salida)
                            | Salida: <strong>{{ \Carbon\Carbon::parse($fichajeHoy->hora_salida)->format('H:i') }}</strong>
                            | Total: <strong>{{ number_format($fichajeHoy->horas_trabajadas, 1) }}h</strong>
                        @else
                            - <span class="text-warning">Pendiente fichar salida</span>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>No has fichado hoy.</strong> Usa el botón "Fichar Entrada" para comenzar tu jornada.
            </div>
        @endif
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-clock-history text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">{{ ($esTrabajador ?? false) ? 'Mis Fichajes' : 'Total Fichajes' }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_fichajes']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pendientes</h6>
                            <h3 class="mb-0">{{ number_format($stats['pendientes_validar']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-hourglass-split text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Horas Mes</h6>
                            <h3 class="mb-0">{{ number_format($stats['horas_totales'], 1) }}h</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-clock-fill text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Horas Extra</h6>
                            <h3 class="mb-0">{{ number_format($stats['horas_extra'], 1) }}h</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters (simplificados para trabajador) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('fichajes.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control"
                           value="{{ request('fecha_desde', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control"
                           value="{{ request('fecha_hasta', now()->endOfMonth()->format('Y-m-d')) }}">
                </div>

                @if(!($esTrabajador ?? false))
                    <div class="col-md-3">
                        <label class="form-label">Trabajador</label>
                        <select name="trabajador_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}" {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>
                                    {{ $trabajador->nombre }} {{ $trabajador->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="validado" class="form-select">
                            <option value="">Todos</option>
                            <option value="1" {{ request('validado') === '1' ? 'selected' : '' }}>Validados</option>
                            <option value="0" {{ request('validado') === '0' ? 'selected' : '' }}>Pendientes</option>
                        </select>
                    </div>
                @else
                    <div class="col-md-3">
                        <label class="form-label">Obra</label>
                        <select name="obra_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($obras as $obra)
                                <option value="{{ $obra->id }}" {{ request('obra_id') == $obra->id ? 'selected' : '' }}>
                                    {{ $obra->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if(!($esTrabajador ?? false))
            <form id="validarMultipleForm" action="{{ route('fichajes.validar-multiple') }}" method="POST">
                @csrf
            @endif
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                @if(!($esTrabajador ?? false))
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                @endif
                                <th>Fecha</th>
                                @if(!($esTrabajador ?? false))
                                <th>Trabajador</th>
                                @endif
                                <th>Obra</th>
                                <th class="text-center">Entrada</th>
                                <th class="text-center">Salida</th>
                                <th class="text-center">Ubicación</th>
                                <th class="text-center">Horas</th>
                                <th class="text-center">Extra</th>
                                <th class="text-center">Estado</th>
                                @if(!($esTrabajador ?? false))
                                <th width="120">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fichajes as $fichaje)
                                <tr class="{{ $fichaje->fecha->isToday() ? 'table-info' : '' }}">
                                    @if(!($esTrabajador ?? false))
                                    <td>
                                        @if(!$fichaje->validado)
                                            <input type="checkbox" class="form-check-input fichaje-checkbox"
                                                   name="fichajes[]" value="{{ $fichaje->id }}">
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <strong>{{ $fichaje->fecha->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $fichaje->fecha->translatedFormat('l') }}</small>
                                        @if($fichaje->fecha->isToday())
                                            <span class="badge bg-info ms-1">Hoy</span>
                                        @endif
                                    </td>
                                    @if(!($esTrabajador ?? false))
                                    <td>
                                        @can('ver_trabajadores')
                                            <a href="{{ route('trabajadores.show', $fichaje->trabajador) }}" class="text-decoration-none">
                                                {{ $fichaje->trabajador->nombre }} {{ $fichaje->trabajador->apellidos }}
                                            </a>
                                        @else
                                            {{ $fichaje->trabajador->nombre }} {{ $fichaje->trabajador->apellidos }}
                                        @endcan
                                    </td>
                                    @endif
                                    <td>
                                        @if($fichaje->obra)
                                            @can('ver_obras')
                                                <a href="{{ route('obras.show', $fichaje->obra) }}" class="text-decoration-none">
                                                    {{ Str::limit($fichaje->obra->nombre, 25) }}
                                                </a>
                                            @else
                                                {{ Str::limit($fichaje->obra->nombre, 25) }}
                                            @endcan
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->hora_entrada)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                {{ \Carbon\Carbon::parse($fichaje->hora_entrada)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->hora_salida)
                                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                                {{ \Carbon\Carbon::parse($fichaje->hora_salida)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->latitud_entrada || $fichaje->latitud_salida)
                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                @if($fichaje->latitud_entrada && $fichaje->longitud_entrada)
                                                    <a href="https://www.google.com/maps?q={{ $fichaje->latitud_entrada }},{{ $fichaje->longitud_entrada }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-success py-0 px-2"
                                                       title="Ver entrada en Google Maps: {{ number_format($fichaje->latitud_entrada, 6) }}, {{ number_format($fichaje->longitud_entrada, 6) }}">
                                                        <i class="bi bi-geo-alt-fill me-1"></i>Entrada
                                                    </a>
                                                @endif
                                                @if($fichaje->latitud_salida && $fichaje->longitud_salida)
                                                    <a href="https://www.google.com/maps?q={{ $fichaje->latitud_salida }},{{ $fichaje->longitud_salida }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-danger py-0 px-2"
                                                       title="Ver salida en Google Maps: {{ number_format($fichaje->latitud_salida, 6) }}, {{ number_format($fichaje->longitud_salida, 6) }}">
                                                        <i class="bi bi-geo-alt-fill me-1"></i>Salida
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->horas_trabajadas)
                                            <strong>{{ number_format($fichaje->horas_trabajadas, 1) }}h</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->horas_extra > 0)
                                            <span class="badge bg-info">+{{ number_format($fichaje->horas_extra, 1) }}h</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($fichaje->validado)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-lg me-1"></i>Validado
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Pendiente
                                            </span>
                                        @endif
                                        @if($fichaje->corregido)
                                            <br><small class="text-muted"><i class="bi bi-pencil"></i> Corregido</small>
                                        @endif
                                    </td>
                                    @if(!($esTrabajador ?? false))
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('editar_fichajes')
                                            <a href="{{ route('fichajes.edit', $fichaje) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan

                                            @if(!$fichaje->validado)
                                                @can('validar_fichajes')
                                                <button type="button" class="btn btn-outline-success btn-validar-individual"
                                                        data-id="{{ $fichaje->id }}" title="Validar">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                @endcan
                                            @endif

                                            @can('eliminar_fichajes')
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="confirmarEliminar({{ $fichaje->id }})" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($esTrabajador ?? false) ? 8 : 11 }}" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No hay fichajes para mostrar
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(!($esTrabajador ?? false))
                    @if($fichajes->hasPages())
                        <div class="d-flex justify-content-between align-items-center p-3 border-top">
                            <div>
                                @can('validar_fichajes')
                                <button type="submit" class="btn btn-success" id="btnValidarMultiple" disabled>
                                    <i class="bi bi-check-all me-2"></i>Validar Seleccionados
                                </button>
                                @endcan
                            </div>
                            <div>
                                {{ $fichajes->withQueryString()->links() }}
                            </div>
                        </div>
                    @else
                        <div class="p-3 border-top">
                            @can('validar_fichajes')
                            <button type="submit" class="btn btn-success" id="btnValidarMultiple" disabled>
                                <i class="bi bi-check-all me-2"></i>Validar Seleccionados
                            </button>
                            @endcan
                        </div>
                    @endif
                @else
                    @if($fichajes->hasPages())
                        <div class="p-3 border-top">
                            {{ $fichajes->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            @if(!($esTrabajador ?? false))
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Delete Form (solo para admin) -->
@if(!($esTrabajador ?? false))
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Validar Individual Form -->
<form id="validarIndividualForm" method="POST" style="display: none;">
    @csrf
</form>
@endif

@push('scripts')
<script>
    @if(!($esTrabajador ?? false))
    // Select all checkboxes
    const selectAllEl = document.getElementById('selectAll');
    if (selectAllEl) {
        selectAllEl.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.fichaje-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateValidarButton();
        });
    }

    // Update validar button state
    document.querySelectorAll('.fichaje-checkbox').forEach(cb => {
        cb.addEventListener('change', updateValidarButton);
    });

    function updateValidarButton() {
        const checked = document.querySelectorAll('.fichaje-checkbox:checked').length;
        const btn = document.getElementById('btnValidarMultiple');
        if (btn) {
            btn.disabled = checked === 0;
            btn.innerHTML = checked > 0
                ? '<i class="bi bi-check-all me-2"></i>Validar (' + checked + ')'
                : '<i class="bi bi-check-all me-2"></i>Validar Seleccionados';
        }
    }

    // Delete confirmation
    function confirmarEliminar(id) {
        Swal.fire({
            title: '¿Eliminar fichaje?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/fichajes/' + id;
                form.submit();
            }
        });
    }

    // Validar individual (botón en columna acciones)
    document.querySelectorAll('.btn-validar-individual').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const form = document.getElementById('validarIndividualForm');
            form.action = '/fichajes/' + id + '/validar';
            form.submit();
        });
    });
    @endif

    @if(($esTrabajador ?? false) && ($fichajeHoy ?? null) && $fichajeHoy->hora_entrada && !$fichajeHoy->hora_salida)
    // Botón fichar salida para trabajador
    const btnSalida = document.getElementById('btnFicharSalida');
    if (btnSalida) {
        btnSalida.addEventListener('click', function() {
            // Obtener GPS antes de fichar salida
            btnSalida.disabled = true;
            btnSalida.innerHTML = '<i class="bi bi-geo-alt me-2"></i>Obteniendo ubicación...';

            let lat = null, lng = null;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        lat = position.coords.latitude;
                        lng = position.coords.longitude;
                        enviarFichajeSalida(lat, lng);
                    },
                    function(error) {
                        // Fichar sin GPS si hay error
                        enviarFichajeSalida(null, null);
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            } else {
                enviarFichajeSalida(null, null);
            }
        });
    }

    function enviarFichajeSalida(lat, lng) {
        fetch('{{ route("fichajes.check-out") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                trabajador_id: {{ $trabajadorActual->id ?? 0 }},
                latitud: lat,
                longitud: lng
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Salida registrada!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
                document.getElementById('btnFicharSalida').disabled = false;
                document.getElementById('btnFicharSalida').innerHTML = '<i class="bi bi-box-arrow-right me-2"></i>Fichar Salida';
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error al registrar la salida', 'error');
            document.getElementById('btnFicharSalida').disabled = false;
            document.getElementById('btnFicharSalida').innerHTML = '<i class="bi bi-box-arrow-right me-2"></i>Fichar Salida';
        });
    }
    @endif
</script>
@endpush
@endsection
