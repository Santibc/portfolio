@php
    $formaciones = $formaciones ?? [];
@endphp

@if(count($formaciones) === 0)
    <div class="text-center text-muted py-4">
        <i class="bi bi-mortarboard fs-1 d-block mb-2"></i>
        <p class="mb-0">No tienes formaciones registradas</p>
    </div>
@else
    <ul class="list-group list-group-flush">
        @foreach($formaciones as $formacion)
            @php
                $rowClass = 'formacion-vigente';
                $estadoBadge = '<span class="badge bg-success">Vigente</span>';

                if (($formacion['estado'] ?? '') === 'caducada') {
                    $rowClass = 'formacion-caducada';
                    $estadoBadge = '<span class="badge bg-danger">Caducada</span>';
                } elseif (($formacion['estado'] ?? '') === 'por_caducar') {
                    $rowClass = 'formacion-por-caducar';
                    $estadoBadge = '<span class="badge bg-warning">Por caducar</span>';
                }
            @endphp
            <li class="list-group-item {{ $rowClass }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ $formacion['tipo'] ?? 'Formacion' }}</strong>
                        <br><small class="text-muted">Obtenido: {{ $formacion['fecha_realizacion'] ?? '-' }}</small>
                        @if(isset($formacion['fecha_caducidad']) && $formacion['fecha_caducidad'])
                            <br><small>Caduca: {{ $formacion['fecha_caducidad'] }}</small>
                        @else
                            <br><small class="text-success">Sin caducidad</small>
                        @endif
                    </div>
                    {!! $estadoBadge !!}
                </div>
            </li>
        @endforeach
    </ul>
@endif
