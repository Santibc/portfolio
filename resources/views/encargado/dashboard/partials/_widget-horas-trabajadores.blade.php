@php
    $trabajadores = $trabajadores ?? [];
@endphp

@if(count($trabajadores) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-people fs-1 d-block mb-2"></i>
        <p class="mb-0">Sin trabajadores asignados</p>
    </div>
@else
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Trabajador</th>
                <th>Obra</th>
                <th class="text-end">Hoy</th>
                <th class="text-end">Semana</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trabajadores as $t)
                <tr>
                    <td>{{ $t['nombre_completo'] }}</td>
                    <td><small class="text-muted">{{ $t['obra_actual'] ?? '-' }}</small></td>
                    <td class="text-end"><strong>{{ $t['horas_hoy'] }}h</strong></td>
                    <td class="text-end">{{ $t['horas_semana'] }}h</td>
                    <td class="text-center">
                        @if($t['fichaje_activo'] ?? false)
                            <span class="badge bg-success fichaje-activo"><i class="bi bi-play-circle"></i> Activo</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-stop-circle"></i></span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
