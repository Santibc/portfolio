@php
    $obras = $obras ?? [];
@endphp

@if(count($obras) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        <p class="mb-0">No tienes obras asignadas</p>
    </div>
@else
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th>Obra</th>
                <th>Cliente</th>
                <th class="text-center">Trab.</th>
                <th>Ultimo Parte</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($obras as $obra)
                @php
                    $estadoClass = match($obra['estado'] ?? '') {
                        'aprobada' => 'info',
                        'en_curso' => 'success',
                        'pausada' => 'warning',
                        'finalizada' => 'secondary',
                        default => 'secondary'
                    };
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('obras.show', $obra['id']) }}" class="text-decoration-none fw-semibold">
                            {{ $obra['codigo'] }}
                        </a>
                        <br><small class="text-muted">{{ Str::limit($obra['nombre'], 25) }}</small>
                    </td>
                    <td><small>{{ Str::limit($obra['cliente'], 20) }}</small></td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $obra['trabajadores_activos'] }}</span>
                    </td>
                    <td><small>{{ $obra['ultimo_parte_fecha'] ?? '-' }}</small></td>
                    <td>
                        <span class="badge bg-{{ $estadoClass }}">{{ $obra['estado'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
