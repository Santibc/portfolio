@php
    $fichajes = $fichajes ?? [];
    $resumen = $resumen ?? [];
@endphp

@if(count($fichajes) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-clock fs-1 d-block mb-2"></i>
        <p class="mb-0">No hay fichajes en este periodo</p>
    </div>
@else
    <div class="p-3 bg-light border-bottom">
        <div class="row text-center">
            <div class="col-4">
                <h5 class="mb-0 text-primary">{{ $resumen['total_horas'] ?? 0 }}h</h5>
                <small class="text-muted">Total Horas</small>
            </div>
            <div class="col-4">
                <h5 class="mb-0 text-success">{{ $resumen['dias_trabajados'] ?? 0 }}</h5>
                <small class="text-muted">Dias Trabajados</small>
            </div>
            <div class="col-4">
                <h5 class="mb-0 text-info">{{ $resumen['horas_extra'] ?? 0 }}h</h5>
                <small class="text-muted">Horas Extra</small>
            </div>
        </div>
    </div>

    <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Fecha</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th class="text-end">Horas</th>
                <th>Obra</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fichajes as $fichaje)
                <tr>
                    <td><small>{{ $fichaje['fecha'] ?? '-' }}</small></td>
                    <td><span class="badge bg-success">{{ $fichaje['hora_entrada'] ?? '-' }}</span></td>
                    <td><span class="badge bg-secondary">{{ $fichaje['hora_salida'] ?? '-' }}</span></td>
                    <td class="text-end"><strong>{{ $fichaje['horas_trabajadas'] ?? '-' }}</strong></td>
                    <td><small class="text-muted">{{ Str::limit($fichaje['obra_codigo'] ?? '-', 15) }}</small></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
