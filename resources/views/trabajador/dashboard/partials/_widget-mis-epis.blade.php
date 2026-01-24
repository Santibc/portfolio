@php
    $epis = $epis ?? [];
@endphp

@if(count($epis) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-shield fs-1 d-block mb-2"></i>
        <p class="mb-0">No tienes EPIs asignados</p>
    </div>
@else
    <ul class="list-group list-group-flush">
        @foreach($epis as $epi)
            @php
                $rowClass = '';
                $estadoBadge = '';

                if (($epi['estado'] ?? '') === 'caducado') {
                    $rowClass = 'epi-caducado';
                    $estadoBadge = '<span class="badge bg-danger">Caducado</span>';
                } elseif (($epi['estado'] ?? '') === 'por_caducar') {
                    $rowClass = 'epi-por-caducar';
                    $estadoBadge = '<span class="badge bg-warning">Por caducar</span>';
                } else {
                    $estadoBadge = '<span class="badge bg-success">Vigente</span>';
                }
            @endphp
            <li class="list-group-item {{ $rowClass }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $epi['nombre'] ?? 'EPI' }}</strong>
                        <br><small class="text-muted">{{ $epi['categoria'] ?? '' }}</small>
                        <br><small>Entregado: {{ $epi['fecha_entrega'] ?? '-' }}</small>
                        @if(isset($epi['fecha_caducidad']) && $epi['fecha_caducidad'])
                            <br><small>Caduca: {{ $epi['fecha_caducidad'] }}</small>
                        @endif
                    </div>
                    {!! $estadoBadge !!}
                </div>
            </li>
        @endforeach
    </ul>
@endif
